<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SystemPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function general()
    {
        if (Auth::user()->group_id > 2) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        if ($this->data["logged_user"]->group_id != 1) return redirect()->route('dashboard');
        $this->data["page_title"] = "Settings - " . get_option('ak_app_title');
        return view('backend.pages.settings.general', $this->data);
    }

    public function advertisements()
    {
        if (Auth::user()->group_id > 2) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        if ($this->data["logged_user"]->group_id != 1) return redirect()->route('dashboard');
        $this->data["page_title"] = "Advertisements - " . get_option('ak_app_title');
        $this->data["categories"] = Category::whereNull('parent')->get();
        return view('backend.pages.settings.advertisements', $this->data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:system_preferences',
            'value' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $tag = SystemPreference::query()->create([
            "title" => $request->post("title"),
            "slug" => $request->post("slug"),
            "value" => $request->post("value"),
            "updated_by" => Auth::id(),
        ]);

        if ($tag) {
            return response()->json(['status' => status_success, 'message' => "The setting has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The setting could not be added. Please try again."], Response::HTTP_OK);
        }
    }

    public function store_options(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            foreach ($request->all() as $key => $value) {
                if ($key !== "_token") {
                    $setting = SystemPreference::where("slug", "=", $key)->first();
                    if ($setting) {
                        $setting->value = $value;
                        $setting->updated_by = Auth::id();
                        $setting->updated_at = date('Y-m-d H:i:s');
                        if (!$setting->save()) return false;
                    } else {
                        $title = str_replace("ak_", "", $key);
                        $title = ucwords(strtolower(str_replace("_", " ", $title)));
                        $setting = SystemPreference::query()->create([
                            "title" => $title,
                            "slug" => $key,
                            "value" => $value,
                            "updated_by" => Auth::id(),
                            "updated_at" => date('Y-m-d H:i:s')
                        ]);
                        if (!$setting) return false;
                    }

                    //Check if theme has changed and replace it
                    if ($setting->slug == "ak_theme") {
                        if (get_option("ak_theme") != $setting->value) {
                            Artisan::call('theme:link');
                        }
                    }

                    //Update cache
                    replace_cache_option($setting->slug, $setting->value);
                }
            }
            return true;
        }, 2);

        if ($result) {
            return response()->json(['status' => status_success, 'message' => "The settings were saved."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The setting could not be saved. Please try again."], Response::HTTP_OK);
        }
    }

    public function update(Request $request)
    {
        $setting = SystemPreference::where("id", "=", $request->post("id"))->first();
        if (!$setting) return response()->json(['status' => status_error, 'message' => "Setting not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:system_preferences,slug,' . $setting->id,
            'value' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $setting->title = $request->post("title");
        $setting->slug = $request->post("slug");
        $setting->value = $request->post("value");
        $setting->updated_by = Auth::id();

        if ($setting->save()) {
            return response()->json(['status' => status_success, 'message' => "The setting has been saved."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The setting could not be saved. Please try again."], Response::HTTP_OK);
        }
    }

    public function destroy($id)
    {
        $setting = SystemPreference::where("id", "=", $id)->first();
        if (!$setting) {
            return response()->json(['status' => status_error, 'message' => "The setting does not exist."], Response::HTTP_OK);
        }

        if ($setting->forceDelete()) return response()->json(['status' => status_success, 'message' => "The tag has been deleted."], Response::HTTP_OK);
        else return response()->json(['status' => status_error, 'message' => "The tag was not deleted."], Response::HTTP_OK);;
    }
}
