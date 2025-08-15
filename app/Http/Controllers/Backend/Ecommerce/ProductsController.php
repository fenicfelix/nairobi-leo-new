<?php

namespace App\Http\Controllers\Backend\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use App\Models\ecommerce\Product;
use App\Models\PostAuthor;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Intervention\Image\Facades\Image as Img;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = "all")
    {
        if (Auth::user()->group_id == 5) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        unset_current_editor();
        $this->data["page_title"] = "Products - " . get_option('ak_app_title');
        return view('backend.pages.ecommerce.products.index', $this->data);
    }

    public function filtered_posts($type)
    {
        $this->data["page_title"] = "Posts - " . get_option('ak_app_title');
        return view('backend.pages.ecommerce.products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data["is_product"] = true;
        $this->data["page_title"] = "New Post - " . get_option('ak_app_title');
        $this->data["tags"] = $this->fetch_tags();
        $this->data["authors"] = [];
        if (can_publish(Auth::user())) $this->data["authors"] = User::where("active", "=", "1")->get();
        $this->data["categories"] = Category::all();
        $this->data["post"] = [];
        return view('backend.pages.ecommerce.products.edit', $this->data);
    }

    public function fetch_tags()
    {
        $tags = Tag::all();
        $results = [];
        foreach ($tags as $tag) {
            $results[] = ["name" => $tag->name];
        }

        return $results;
    }

    private function get_tag_id($tag_name)
    {
        $tag_data = [
            "name" => $tag_name,
            "slug" => Str::slug($tag_name, "-")
        ];
        try {
            $tag = Tag::query()->firstOrCreate($tag_data);
        } catch (\Throwable $th) {
            $tag = Tag::where("slug", "=", Str::slug($tag_name, "-"))->first();
        }
        return $tag->id;
    }

    public function upload_file(Request $request)
    {
        request()->validate([
            'file'  => 'required|mimes:jpg,jpeg,png,doc,docx,pdf,txt|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $filenamewithextension = $request->file('file')->getClientOriginalName(); //get filename with extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME); //get filename without extension
            $extension = $request->file('file')->getClientOriginalExtension(); //get file extension

            $unique = time();
            $filenametostore = $filename . '-' . $unique . '.' . $extension;
            $path = 'uploads/' . date('Y') . '/' . date('m');
            $public_path = "public/" . $path;
            $storage_path = 'storage/' . $path . '/';
            $original_file_path = $path . "/" . $filenametostore;

            //Save original
            $request->file('file')->storeAs($public_path, $filenametostore);

            //Generate thumbnails
            $thumbnail_sizes = get_thumbnail_sizes();
            if ($thumbnail_sizes) {
                foreach ($thumbnail_sizes as $key => $value) {
                    $thumbnail = $filename . '-' . $unique . config('cms.thumbnail_separator') . $key . '.' . $extension;
                    $request->file('file')->storeAs($public_path, $thumbnail);

                    $thumbnail = public_path($storage_path . $thumbnail);
                    try {
                        $this->createThumbnail($thumbnail, $value, $value);
                    } catch (Exception $e) {
                        info($e->getMessage());
                    }
                }
            }

            $user_id = Auth::id();
            $file_data = Image::query()->create([
                "file_name" => $original_file_path,
                "uploaded_by" => $user_id,
                "updated_by" => $user_id,
            ]);

            $preview_url = Storage::disk('public')->url($original_file_path);

            return response()->json(['status' => status_success, 'message' => "The file has been uploaded.", "file_id" => $file_data->id, "preview_url" => $preview_url], Response::HTTP_OK);
        }
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Img::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    public function upload_intext_file(Request $request)
    {
        request()->validate([
            'file'  => 'required|mimes:jpg,jpeg,png,doc,docx,pdf,txt,webp|max:2048',
        ]);

        if ($request->file('file')) {
            $file = $request->file('file');
            $name = rand(1, 9999) . "-" . $file->getClientOriginalName();
            $path = 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d');
            $uploaded_file = Storage::disk('public')->putFileAs($path, $file, $name);
            $preview_url = Storage::disk('public')->url($uploaded_file);

            $user_id = Auth::id();
            Image::query()->create([
                "file_name" => $uploaded_file,
                "uploaded_by" => $user_id,
                "updated_by" => $user_id,
            ]);

            return response()->json(['status' => status_success, 'message' => "The file has been uploaded.", "preview_url" => $preview_url], Response::HTTP_OK);
        }
    }

    public function update_image_tags(Request $request)
    {
        $image = Image::where("id", "=", $request->post("id"))->first();
        if (!$image) return response()->json(['status' => status_error, 'message' => "Image was not found."], Response::HTTP_OK);

        $image->title = $request->post("title");
        $image->alt_text = $request->post("alt_text");
        $image->caption = $request->post("caption");
        $image->description = $request->post("description");
        $image->updated_by = Auth::id();

        if (!$image->save()) return response()->json(['status' => status_error, 'message' => "The image was not updated."], Response::HTTP_OK);

        $preview_url = Storage::disk('public')->url($image->file_name);
        return response()->json(['status' => status_success, "preview_url" => $preview_url, 'message' => "The image has been updated."], Response::HTTP_OK);
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
        $this->data["page_title"] = "View Post - " . get_option('ak_app_title');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        unset_current_editor(); //Unset all posts I am currently
        $this->data["is_product"] = true;
        $this->data["page_title"] = "Edit Post - " . get_option('ak_app_title');
        $post = Post::with(["main_image", "authors:id,display_name", "tags:id,name"])->where("id", "=", $id)->first();
        if (!$post) {
            redirect("products.index");
        }

        $show_editor_modal = false;
        if (is_null($post->current_editor)) {
            set_current_editor($post->id);
        } else if ($post->current_editor != Auth::id()) {
            $show_editor_modal = true;
        }

        $this->data["post"] = $post;
        $this->data["product"] = Product::where('post_id', $post->id)->first();
        $this->data["tags"] = $this->fetch_tags();
        $this->data["authors"] = User::where("active", "=", "1")->get();
        $this->data["categories"] = Category::all();
        $this->data["show_editor_modal"] = $show_editor_modal;
        $this->data["preview_url"] = route("preview", $post->id);
        return view('backend.pages.ecommerce.products.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create_update_post(Request $request)
    {
        if ($request->post("id")) {
            $validator = Validator::make($request->all(), [
                'slug' =>  'required|min:3|unique:posts,slug,' . $request->post('id')
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => status_error, 'message' => "The title has already been taken."], Response::HTTP_OK);
            }

            return $this->update_post($request);
        } else {
            return $this->create_post($request);
        }
    }

    private function update_post($request)
    {
        $can_publish = false;
        $post = Post::where("id", "=", $request->post("id"))->first();
        if (!$post) return response()->json(['status' => status_error, 'message' => "Post was not found."], Response::HTTP_OK);

        if ($post->current_editor != Auth::id()) {
            session()->flash('status', "You are not allowed to edit the post.");
            return response()->json(['status' => status_cannot_edit, 'message' => route('posts.index', 'all')], Response::HTTP_OK);
        }


        $update = DB::transaction(function () use ($request, $post) {

            $post->title = $request->post("title");
            $post->slug = $request->post("slug");
            $post->body = $request->post("body");
            $post->in_summary = $request->post("in_summary");
            $post->excerpt = $request->post("excerpt");
            $post->seo_keywords = $request->post("seo_keywords");
            $post->seo_title = $request->post("seo_title");
            $post->seo_description = $request->post("seo_description");
            $post->seo_status = $request->post("seo_status") ?? "0";
            $post->featured_image = $request->post("featured_image");
            $post->category_id = $request->post("category_id") ?? 1;
            $post->post_label = $request->post("post_label");
            $post->homepage_ordering = 0;
            $post->is_breaking = 0;
            $post->is_featured = 0;
            $post->is_sponsored = 0;
            $post->display_ads = 0;

            $date = date("Y-m-d H:i:s");
            $post->last_updated_at = $date;

            try {
                if ($request->post("homepage_ordering")) $post->homepage_ordering = $request->post("homepage_ordering") ?? 0;
                if ($request->post("is_breaking")) $post->is_breaking = ($request->post("is_breaking") == "on") ? "1" : "0";
                if ($request->post("is_featured")) $post->is_featured = ($request->post("is_featured") == "on") ? "1" : "0";
                if ($request->post("is_sponsored")) $post->is_sponsored = ($request->post("is_sponsored") == "on") ? "1" : "0";
                if ($request->post("display_ads")) $post->display_ads = ($request->post("display_ads") == "on") ? "1" : "0";
            } catch (Throwable $th) {
                //throw $th;
            }

            if ($request->post("task") == "publish") {
                if (in_array($post->status_id, [1, 2])) {
                    if ($request->post("publish_type") == "immediate") {
                        $post->status_id = 3;
                        $post->published_at = $date;
                        $post->published_by = Auth::id();
                    } else {
                        $post->status_id = 2; //Schedule post
                        $post->published_at = ($request->post("schedule_time")) ? date("Y-m-d H:i:s", strtotime($request->post("schedule_time"))) : date('Y-m-d H:i:s');
                        $post->published_by = Auth::id();
                    }
                }
            }

            if ($request->post("task") == "publish" && $request->post("send_notification") && $post->notification_sent == "0") $this->send_push_notification($post->id);

            if ($request->post("schedule_time")) $post->published_at = date("Y-m-d H:i:s", strtotime($request->post("schedule_time")));

            try {
                $post->save();
            } catch (\Throwable $th) {
                return false;
            }

            $tags = explode(",", $request->post("tags"));
            DB::delete('delete from post_tags where post_id = ?', [$post->id]);
            foreach ($tags as $tag) {
                if ($tag != "") {
                    $tag_data = [
                        "post_id" => $post->id,
                        "tag_id" => $this->get_tag_id($tag)
                    ];
                    if (!PostTag::query()->firstOrCreate($tag_data)) return false;
                }
            }

            if ($request->post("authors")) {
                DB::delete('delete from post_authors where post_id = ?', [$post->id]);
                foreach ($request->post("authors") as $author) {
                    $author_data = [
                        "post_id" => $post->id,
                        "author_id" => $author
                    ];
                    if (!PostAuthor::query()->firstOrCreate($author_data)) return false;;
                }
            }

            if ($request->post("homepage_ordering") != "0") clear_homepage_ordering($post->id, $request->post("homepage_ordering"));

            if ($request->post("is_breaking")) clear_breaking_stories($post->id);

            return true;
        }, 2);

        if (!$update) return response()->json(['status' => status_error, 'message' => "The post could not be updated."], Response::HTTP_OK);

        if ($post->title && $post->status_id != 3) {
            $can_publish = true;
        }

        return response()->json(['status' => status_success, 'message' => "The post has been updated.", "preview_url" => route("preview", $post->id), "can_publish" => $can_publish], Response::HTTP_OK);
    }

    private function create_post($request)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:posts'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => "The title has already been taken."], Response::HTTP_OK);
        }

        $user_id = Auth::id();
        $postId = DB::transaction(function () use ($request, $user_id) {
            $data = [
                "title" => $request->post("title"),
                "slug" => $request->post("slug"),
                "excerpt" => $request->post("excerpt"),
                "in_summary" => $request->post("in_summary"),
                "body" => $request->post("body"),
                "category_id" => $request->post("category_id") ?? 1,
                "seo_keywords" => $request->post("seo_keywords"),
                "seo_title" => $request->post("seo_title"),
                "seo_description" => $request->post("seo_description"),
                "seo_status" => $request->post("seo_status") ?? "0",
                "is_breaking" => ($request->post("is_breaking") == "on") ? "1" : "0",
                "is_featured" => ($request->post("is_featured") == "on") ? "1" : "0",
                "is_sponsored" => ($request->post("is_sponsored") == "on") ? "1" : "0",
                "display_ads" => ($request->post("display_ads") == "on") ? "1" : "0",
                "featured_image" => ($request->post("featured_image")) ?? NULL,
                "post_label" => $request->post("post_label"),
                "homepage_ordering" => ($request->post("homepage_ordering")) ? $request->post("homepage_ordering") : '0',
                "created_by" => $user_id,
                "current_editor" => $user_id,
                "status_id" => 1,
            ];

            $post = Post::query()->create($data);
            $tags = explode(",", $request->post("tags"));

            if ($tags) {
                foreach ($tags as $tag) {
                    if ($tag != "") {
                        $tag_data = [
                            "post_id" => $post->id,
                            "tag_id" => $this->get_tag_id($tag)
                        ];
                        PostTag::query()->firstOrCreate($tag_data);
                    }
                }
            }

            if ($request->post("authors")) {
                foreach ($request->post("authors") as $author) {
                    $author_data = [
                        "post_id" => $post->id,
                        "author_id" => $author
                    ];
                    PostAuthor::query()->firstOrCreate($author_data);
                }
            }

            if ($request->post("homepage_ordering") != "0") clear_homepage_ordering($post->id, $request->post("homepage_ordering"));

            if ($request->post("is_breaking")) clear_breaking_stories($post->id);

            return $post->id;
        }, 2);

        if (!$postId) {
            return response()->json(['status' => status_error, 'message' => "The was not saved."], Response::HTTP_OK);
        }

        $result = [
            'status' => status_success,
            'message' => "The post has been saved.",
            "id" => $postId,
            "preview_url" => route("preview", $postId),
            "change_values" => true,
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::where("id", "=", $id)->first();
        if (!$post) {
            return response()->json(['status' => status_error, 'message' => "The post does not exist."], Response::HTTP_OK);
        }

        $post->status_id = 4;
        if (!$post->save()) return response()->json(['status' => status_error, 'message' => "The post was not trashed."], Response::HTTP_OK);

        return response()->json(['status' => status_success, 'message' => "The post has been trashed."], Response::HTTP_OK);
    }


    public function recover_post(Request $request)
    {
        $post = Post::where("id", "=", $request->post("identifier"))->first();
        if (!$post) {
            return response()->json(['status' => status_error, 'message' => "The post does not exist."], Response::HTTP_OK);
        }

        $post->status_id = 1;
        if (!$post->save()) return response()->json(['status' => status_error, 'message' => "Please try again."], Response::HTTP_OK);

        return response()->json(['status' => status_success, 'message' => "he post has been moved to drafts."], Response::HTTP_OK);
    }

    public function delete_permanently(Request $request)
    {
        $post = Post::where("id", "=", $request->post("identifier"))->first();
        if (!$post) {
            return response()->json(['status' => status_error, 'message' => "The post does not exist."], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($post) {

            DB::delete('delete from post_tags where post_id = ?', [$post->id]);
            DB::delete('delete from post_authors where post_id = ?', [$post->id]);

            if ($post->forceDelete()) return true;
            else return false;
        }, 2);

        if (!$deleted) {
            return response()->json(['status' => status_error, 'message' => "The post was not deleted."], Response::HTTP_OK);
        }

        return response()->json(['status' => status_success, 'message' => "The post has been permanently deleted."], Response::HTTP_OK);
    }

    private function send_push_notification($postId)
    {
        if (get_option('ak_onesignal_appid') && get_option('ak_onesignal_key')) {
            $post = Post::with(["main_image", "category"])->where("id", "=", $postId)->where('notification_sent', '0')->first();
            if ($post) {
                $permalink = $permalink = route('post', [$post->category->slug, $post->id, $post->slug]);
                $content = array(
                    "en" => $post->title
                );

                if (isset($post->main_image)) {
                    $fields = array(
                        'app_id' => get_option('ak_onesignal_appid'),
                        'included_segments' => array('All'),
                        'data' => array("foo" => "bar"),
                        'chrome_web_image' => fetch_image($post->main_image->file_name, "md"),
                        'contents' => $content,
                        'url' => $permalink
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic ' . get_option('ak_onesignal_key')
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    $post->notification_sent = "1";
                    $post->save();
                } else {
                    info("No image found for post " . $post->id);
                }
            }
        }

        return true;
    }

    public function hide_breaking_news(Request $request)
    {
        if ($request->id) {
            Cookie::queue("post-" . $request->id, 'hide', 30);
            return response()->json(['status' => status_success, 'message' => "Success."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "Failed."], Response::HTTP_OK);
        }
    }

    public function take_over_post(Request $request)
    {
        if ($request->post('post_id')) {
            if (unset_current_editor($request->post('post_id'))) {
                return redirect()->route('posts.edit', $request->post('post_id'));
            }
        }

        return redirect()->route('posts.index', 'all')->with("error", "An error occurred. Please try again later.");
    }
}
