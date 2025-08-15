<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProgramLineup;
use App\Models\Show;
use App\Models\User;
use App\Models\ShowHosts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnSelf;

class ShowsController extends Controller
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
        $this->data["page_title"] = "Shows - " . get_option('ak_app_title');
        return view('backend.pages.shows.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data["page_title"] = "Shows - " . get_option('ak_app_title');
        $this->data["hosts"] = User::where("active", "=", "1")->get();
        return view('backend.pages.shows.create', $this->data);
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
            'title' => 'required|string|min:3|max:255',
            'slug' => 'required|string|min:3|max:255|unique:shows',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user_id = Auth::id();
        $date = date("Y-m-d H:i:s");

        $show = Show::query()->create([
            "title" => $request->post("title"),
            "slug" => $request->post("slug"),
            "synopsis" => $request->post("synopsis"),
            "hosts" => $request->post("hosts"),
            "description" => $request->post("description"),
            "seo_keywords" => $request->post("seo_keywords"),
            "seo_title" => $request->post("seo_title"),
            "seo_description" => $request->post("seo_description"),
            "seo_status" => $request->post("seo_status"),
            "banner_img" => ($request->post("banner_img")) ?? NULL,
            "mobile_img" => ($request->post("mobile_img")) ?? NULL,
            "created_by" => $user_id,
            "last_updated_by" => $user_id,
            "active" => 1,
        ]);

        // foreach ($request->post("hosts") as $host) {
        //     $host_data = [
        //         "show_id" => $show->id,
        //         "host_id" => $host
        //     ];
        //     ShowHosts::query()->firstOrCreate($host_data);
        // }

        if ($show) {
            return response()->json(['status' => status_success, 'message' => $request->post("title") . " has been added."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => $request->post("title") . " was not added."], Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data["page_title"] = "Shows - " . get_option('ak_app_title');
        $this->data["hosts"] = User::where("active", "=", "1")->get();
        $this->data["show"] = Show::with(["hosts", "mobile_banner", "banner"])->where("id", "=", $id)->first();
        return view('backend.pages.shows.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        info(json_encode($request->all()));
        die;
        $show = Show::where("id", "=", $id)->first();
        if (!$show) {
            return response()->json(['status' => status_error, 'message' => "Show not found."], Response::HTTP_OK);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'slug' => 'required|string|min:3|max:255|unique:shows,slug,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $show->title = $request->post("title");
        $show->slug = $request->post("slug");
        $show->synopsis = $request->post("synopsis");
        $show->hosts = $request->post("hosts");
        $show->description = $request->post("description");
        $show->seo_keywords = $request->post("seo_keywords");
        $show->seo_title = $request->post("seo_title");
        $show->seo_description = $request->post("seo_description");
        $show->seo_status = $request->post("seo_status");
        $show->banner_img = $request->post("banner_img") ?? NULL;
        $show->mobile_img = $request->post("mobile_img") ?? NULL;
        $show->last_updated_by = Auth::id();
        $show->active = ($request->post("active")) ? '1' : '0';

        if ($show->save()) {
            return response()->json(['status' => status_success, 'message' => $request->post("title") . " has been updated."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => $request->post("title") . " was not updated."], Response::HTTP_OK);
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
        $show = Show::where("id", "=", $id)->first();
        if (!$show) {
            return response()->json(['status' => status_error, 'message' => "Show not found."], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($show) {

            if (!DB::table('program_lineups')->where('show_id', $show->id)->delete()) return false;
            if (!$show->forceDelete()) return false;

            return true;
        }, 2);

        if ($deleted) {
            return response()->json(['status' => status_success, 'message' => $show->title . " has been deleted."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => $show->title . " has not deleted."], Response::HTTP_OK);
        }
    }
}
