<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PostTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class TagsController extends Controller
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
        $this->data["page_title"] = "Tags - " . get_option('ak_app_title');
        return view('backend.pages.tags.index', $this->data);
    }

    public function fetch_tags()
    {
        $tags = Tag::all();
        $results = [];
        foreach ($tags as $tag) {
            $results[] = ["name" => $tag->name];
        }

        return response()->json(['status' => status_success, 'tags' => $results], Response::HTTP_OK);
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
            'name' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:tags',
            'seo_status' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $tag = Tag::query()->create([
            "name" => $request->post("name"),
            "slug" => $request->post("slug"),
            "seo_keywords" => $request->post("seo_keywords"),
            "seo_title" => $request->post("seo_title"),
            "seo_description" => $request->post("seo_description"),
            "seo_status" => $request->post("seo_status"),
        ]);

        if ($tag) {
            return response()->json(['status' => status_success, 'message' => "The tag has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The tag could not be added. Please try again."], Response::HTTP_OK);
        }
    }

    public function update_tag(Request $request)
    {
        $tag = Tag::where("id", "=", $request->post("id"))->first();
        if (!$tag) return response()->json(['status' => status_error, 'message' => "Tag not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'name' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:tags,slug,' . $tag->id,
            'seo_status' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $tag->name = $request->post("name");
        $tag->slug = $request->post("slug");
        $tag->seo_keywords = $request->post("seo_keywords");
        $tag->seo_title = $request->post("seo_title");
        $tag->seo_description = $request->post("seo_description");
        $tag->seo_status = $request->post("seo_status");

        if ($tag->save()) {
            return response()->json(['status' => status_success, 'message' => "The tag has been saved."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The tag could not be saved. Please try again."], Response::HTTP_OK);
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
        $tag = Tag::where("id", "=", $id)->first();
        if (!$tag) {
            return response()->json(['status' => status_error, 'message' => "The tag does not exist."], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($tag) {

            //Assign categories to the default category
            if (PostTag::where("id", "=", $tag->id)->exists())
                if (!PostTag::destroy($tag->id)) return false;
            if (!$tag->forceDelete()) return false;

            return true;
        }, 2);

        if ($deleted) {
            return response()->json(['status' => status_success, 'message' => "The tag has been deleted."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The tag was not deleted."], Response::HTTP_OK);
        }
    }
}
