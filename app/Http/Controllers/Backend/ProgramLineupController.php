<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Show;
use App\Models\ProgramLineup;
use App\Models\SystemPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgramLineupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->group_id > 2) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        unset_current_editor();
        $this->data["page_title"] = "Program Lineup - " . get_option('ak_app_title');
        $this->data["shows"] = Show::select('id', 'title', 'slug')->where("active", "=", "1")->orderBy('title', 'ASC')->get();
        $this->data["program_lineup"] = get_option("ak_program_lineup");
        return view('backend.pages.shows.lineup', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $shows = Show::isActive()->get();
        if (!$shows) return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);

        $user_id = Auth::id();

        $update = DB::transaction(function () use ($request, $shows, $user_id) {
            $lineups = json_decode($request->post('data'));

            DB::table('program_lineups')->truncate();
            foreach ($lineups as $lineup) {
                foreach ($lineup->periods as $period) {
                    $data = [
                        'day' => $lineup->day,
                        'start_time' => $period->start,
                        'end_time' => date('H:i:s', strtotime($period->end . " -1 second")),
                        'created_by' => $user_id,
                        'last_updated_by' => $user_id
                    ];

                    foreach ($shows as $show) {
                        if ($show->title == $period->title) {
                            $data["show_id"] = $show->id;
                            break;
                        }
                    }

                    $insert = ProgramLineup::query()->create($data);
                    if (!$insert) return false;
                }
            }

            $setting = SystemPreference::where("slug", "=", "ak_program_lineup")->first();
            if ($setting) {
                $setting->value = json_encode($lineups);
                $setting->updated_by = $user_id;
                $setting->updated_at = date('Y-m-d H:i:s');
                if (!$setting->save()) return false;
            } else {
                $setting = SystemPreference::query()->create([
                    "title" => "Program Lineup",
                    "slug" => "ak_program_lineup",
                    "value" => json_encode($lineups),
                    "updated_by" => $user_id,
                    "updated_at" => date('Y-m-d H:i:s')
                ]);
                if (!$setting) return false;
            }
            replace_cache_option($setting->slug, $setting->value);

            return true;
        }, 2);

        if ($update) {
            return response()->json(['status' => status_success, 'message' => "The lineup has been updated successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The lineup could not be updated. Please try again."], Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = DB::transaction(function () use ($id) {

            DB::table('program_lineups')->truncate();
            info("Cleared");
            $setting = SystemPreference::where("slug", "=", "ak_program_lineup")->first();
            if ($setting) {
                $setting->value = "";
                $setting->updated_by = Auth::id();
                $setting->updated_at = date('Y-m-d H:i:s');
                if (!$setting->save()) return false;
                replace_cache_option($setting->slug, $setting->value);
            }
            return true;
        }, 2);

        if ($deleted) {
            session()->flash('success', 'The lineup has been cleared.');
            return response()->json(['status' => status_success, 'message' => "The lineup has been cleared."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The lineup was not cleared."], Response::HTTP_OK);
        }
    }
}
