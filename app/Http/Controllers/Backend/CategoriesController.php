<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{

    public function index()
    {
        if (Auth::user()->group_id > 2) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        unset_current_editor();
        $this->data["page_title"] = "Categories - " . get_option('ak_app_title');
        $this->data["categories"] = Category::where("active", "=", "1")->orderBy("name", "ASC")->get();
        return view('backend.pages.categories.index', $this->data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:categories',
            'seo_status' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user_id = Auth::id();

        $category = Category::query()->create([
            "name" => $request->post("name"),
            "slug" => $request->post("slug"),
            "parent" => ($request->post("parent")) ? $request->post("parent") : NULL,
            "seo_keywords" => $request->post("seo_keywords"),
            "seo_title" => $request->post("seo_title"),
            "seo_description" => $request->post("seo_description"),
            "seo_status" => $request->post("seo_status"),
            "added_by" => $user_id,
            "updated_by" => $user_id,
        ]);

        if ($category) {
            return response()->json(['status' => status_success, 'message' => "The category has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The category could not be added. Please try again."], Response::HTTP_OK);
        }
    }

    public function update_category(Request $request)
    {
        $category = Category::where("id", "=", $request->post("id"))->first();
        if (!$category) return response()->json(['status' => status_error, 'message' => "Category not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'name' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:categories,slug,' . $category->id,
            'seo_status' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $category->name = $request->post("name");
        $category->slug = $request->post("slug");
        $category->parent = $request->post("parent");
        $category->seo_keywords = $request->post("seo_keywords");
        $category->seo_title = $request->post("seo_title");
        $category->seo_description = $request->post("seo_description");
        $category->seo_status = $request->post("seo_status");
        $category->updated_by = Auth::id();

        try {
            $category->save();
        } catch (\Throwable $th) {
            return response()->json(['status' => status_error, 'message' => "The category could not be saved. Please try again."], Response::HTTP_OK);
        }

        return response()->json(['status' => status_success, 'message' => "The category has been saved."], Response::HTTP_OK);
    }

    public function delete_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' =>  'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($request) {
            $category = Category::where("id", "=", $request->identifier)->first();
            //Assign categories to the default category
            if (Post::where("category_id", "=", $category->id)->exists()) {
                if ($request->category_id) $default_category = Category::where("id", "=", $request->category_id)->first();
                else $default_category = Category::query()->isDefault()->first();
                if ($default_category) {
                    $date = date("Y-m-d H:i:s");
                    Post::where("category_id", $category->id)
                        ->update(
                            [
                                "category_id" => $default_category->id,
                                "last_updated_at" => $date,
                            ]
                        );
                }
            }

            if ($category->forceDelete()) return true;
            else return false;
        }, 2);

        if ($deleted) {
            return response()->json(['status' => status_success, 'message' => "The category has been deleted."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The category was not deleted."], Response::HTTP_OK);
        }
    }
}
