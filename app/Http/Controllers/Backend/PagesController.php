<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
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
        $this->data["page_title"] = "Pages - " . get_option('ak_app_title');
        return view('backend.pages.pages.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data["page_title"] = "New Page - " . get_option('ak_app_title');
        $this->data["categories"] = Category::all();
        return view('backend.pages.pages.create', $this->data);
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
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:pages',
            'seo_status' =>  'required',
            'template' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user_id = Auth::id();

        $page = Page::query()->create([
            "title" => $request->post("title"),
            "slug" => $request->post("slug"),
            "body" => $request->post("body"),
            "seo_keywords" => $request->post("seo_keywords"),
            "seo_title" => $request->post("seo_title"),
            "seo_description" => $request->post("seo_description"),
            "seo_status" => $request->post("seo_status"),
            "category_id" => $request->post("category_id"),
            "template" => $request->post("template"),
            "created_by" => $user_id,
            "updated_by" => $user_id
        ]);

        if ($page) {
            return response()->json(['status' => status_success, 'message' => "The page has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The page could not be added. Please try again."], Response::HTTP_OK);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data["page_title"] = "Edit Page - " . get_option('ak_app_title');
        $page = Page::where("id", "=", $id)->first();
        $this->data["page"] = $page;
        $this->data["categories"] = Category::all();
        return view('backend.pages.pages.edit', $this->data);
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
        $page = Page::where("id", "=", $id)->first();
        if (!$page) return response()->json(['status' => status_error, 'message' => "Page was not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:pages,slug,' . $page->id,
            'seo_status' =>  'required',
            'template' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $page->title = $request->post("title");
        $page->slug = $request->post("slug");
        $page->body = $request->post("body");
        $page->template = $request->post("template");
        $page->category_id = $request->post("category_id");
        $page->seo_keywords = $request->post("seo_keywords");
        $page->seo_title = $request->post("seo_title");
        $page->seo_description = $request->post("seo_description");
        $page->seo_status = $request->post("seo_status");
        $page->updated_by = Auth::id();

        if ($page->save()) {
            return response()->json(['status' => status_success, 'message' => "The page has been updated."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The page could not be updated. Please try again."], Response::HTTP_OK);
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
        //
    }
}
