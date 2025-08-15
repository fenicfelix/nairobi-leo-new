<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
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
        $this->data["page_title"] = "Menus - " . get_option('ak_app_title');
        $this->data["pages"] = Page::orderBy('title', 'ASC')->get(["id", "title", "slug"]);
        $this->data["categories"] = Category::orderBy('name', 'ASC')->where('active', '1')->get(["id", "name as title", "slug"]);
        $this->data["posts"] = Post::isPublished()->with(["category"])->orderBy('published_at', 'DESC')->skip(0)->take(10)->get(['id', 'title', 'slug', 'category_id']);
        $this->data["menus"] = Menu::orderBy('title', 'ASC')->get();
        return view('backend.pages.menus.index', $this->data);
    }

    public function get_menu_items(Request $request)
    {
        $menu_items = MenuItem::where('menu_id', $request->get('id'))->orderBy('order', 'ASC')->get(['title', 'display_title', 'slug', 'type', 'url', 'order', 'reference_id']);
        if ($menu_items) {
            $data = [
                "status" => status_success,
                "data" => $menu_items
            ];
        } else {
            $data = [
                "status" => status_error,
                "data" => "Nothing found"
            ];
        }
        return json_encode($data);
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
            'slug' =>  'required|min:3|unique:menus',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $menu = Menu::query()->create([
            "title" => $request->post("title"),
            "slug" => $request->post("slug"),
        ]);

        if ($menu) {
            return response()->json(['status' => status_success, 'message' => "The menu has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The menu could not be added. Please try again."], Response::HTTP_OK);
        }
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
        //
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
