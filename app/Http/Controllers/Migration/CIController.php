<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Jobs\CleanupInnerImages;
use App\Jobs\GenerateThumbnails;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use App\Models\PostAuthor;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use DOMDocument as GlobalDOMDocument;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image as Img;
use DOMDocument;
use DOMXPath;

class CIController extends Controller
{
    public function index($module, $page)
    {
        return $this->$module($page);
    }

    public function users($page = 0)
    {
        $sql = "select * from db_ketoday.users where id > 1";
        $users = DB::select($sql);
        foreach ($users as $user) {
            $data = [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "display_name" => $user->display_name,
                "username" => $user->username,
                "email" => $user->email,
                "password" => Hash::make('password'),
                "biography" => $user->biography,
                "group_id" => $user->group_id,
                "created_at" => $user->added_on,
                "updated_at" => $user->added_on,
                "facebook" => $user->facebook,
                "twitter" => $user->twitter,
                "linkedin" => $user->linkedin,
                "instagram" => $user->instagram,
                "active" => $user->active,
            ];
            User::query()->create($data);
        }
    }

    public function categories($page = 0)
    {
        $sql = "select * from db_ketoday.categories";
        $categories = DB::select($sql);

        foreach ($categories as $category) {
            $new = [
                "id" => $category->id,
                "name" => $category->name,
                "slug" => $category->slug,
                "default" => '0',
                "added_by" => "1",
                "updated_by" => "1",
            ];
            if (!Category::where("id", "=", $category->id)->exists()) {
                Category::query()->create($new);
            } else {
                $old = Category::where("id", $category->id)->first();
                $old->name = $category->name;
                $old->slug = $category->slug;
                $old->save();
            }
        }
    }

    public function tags($page = 0)
    {
        $limit = 5000;
        $offset = (($limit * $page) - $limit);
        $sql = "select * from db_ketoday.tags limit $limit offset $offset";
        $tags = DB::select($sql);

        foreach ($tags as $tag) {
            $new = [
                "id" => $tag->id,
                "name" => $tag->name,
                "slug" => $tag->slug,
                "seo_title" => $tag->name,
                "seo_description" => $tag->description,
                "seo_status" => '40'
            ];
            if (!Tag::where("slug", "=", $tag->slug)->exists()) {
                Tag::query()->create($new);
            }
        }
    }

    public function images($page = 0)
    {
        $limit = 3000;
        $offset = (($limit * $page) - $limit);
        $sql = "select * from db_ketoday.images limit $limit offset $offset";
        $images = DB::select($sql);

        foreach ($images as $image) {
            $date = date('Y', strtotime($image->uploaded_on)) . "/" . date("m", strtotime($image->uploaded_on));
            $new = [
                "id" => $image->id,
                "file_name" => "uploads/" . $date . "/" . $image->file_name,
                "title" => $image->title,
                "alt_text" => $image->alt_text,
                "caption" => $image->caption,
                "description" => $image->description,
                "uploaded_at" => $image->uploaded_on,
                "uploaded_by" => $image->uploaded_by,
                "updated_at" => $image->uploaded_on,
                "updated_by" => $image->uploaded_by,
            ];
            if (!Image::where("id", "=", $image->id)->exists()) {
                Image::query()->create($new);
            }
        }
    }

    public function posts($page = 0)
    {
        ini_set('memory_limit', '1024M');
        $limit = 1000;
        $offset = (($limit * $page) - $limit);
        $sql = "select * from db_ketoday.posts limit $limit offset $offset";
        $posts = DB::select($sql);
        foreach ($posts as $post) {
            $status_id = "1";
            if ($post->is_published == "1") $status_id = "3";
            if ($post->is_trashed == "1") $status_id = "4";
            $new = [
                "id" => $post->id,
                "title" => $post->title,
                "slug" => $post->slug,
                "excerpt" => $post->excerpt,
                "in_summary" => $post->in_summary,
                "body" => $post->body,
                "category_id" => $post->category_id,
                "seo_keywords" => $post->seo_keywords,
                "seo_title" => $post->seo_title,
                "seo_description" => $post->seo_description,
                "seo_status" => 60,
                "display_ads" => "1",
                "is_featured" => $post->is_featured,
                "is_sponsored" => $post->is_sponsored,
                "total_views" => $post->total_views,
                "post_label" => $post->label,
                "featured_image" => $post->featured_image,
                "homepage_ordering" => $post->homepage_ordering,
                "created_at" => $post->added_on,
                "published_at" => ($post->published_on == "0000-00-00 00:00:00") ? NULL : $post->published_on,
                "last_updated_at" => $post->last_update_on ?? $post->added_on,
                "created_by" => $post->added_by,
                "published_by" => $post->published_by,
                "status_id" => $status_id
            ];
            if (!Post::where("id", "=", $post->id)->orWhere('slug', $post->slug)->exists()) {
                $insert = Post::query()->create($new);
                if ($insert) {
                    $authors = DB::select("select * from db_ketoday.post_authors where post_id = " . $post->id);
                    if ($authors) {
                        foreach ($authors as $author) {
                            PostAuthor::query()->create([
                                "post_id" => $post->id,
                                "author_id" => $author->author_id
                            ]);
                        }
                    }

                    //Post Tags
                    $tags = DB::select("select * from db_ketoday.post_tags where post_id = " . $post->id);
                    if ($tags) {
                        foreach ($tags as $tag) {
                            try {
                                PostTag::query()->create([
                                    "post_id" => $post->id,
                                    "tag_id" => $tag->tag_id
                                ]);
                            } catch (\Throwable $th) {
                                info('OMMITTED POST_TAG: ' . $tag->tag_id);
                            }
                        }
                    }
                }
            } else {
                info('OMMITTED POST: ' . $post->id);
            }
        }
    }

    public function cleanup_images($page)
    {
        ini_set('max_execution_time', '600');
        //$year = "2019";
        // $path = [
        //     "uploads/$year/01/", "uploads/$year/02/", "uploads/$year/03/", "uploads/$year/04/", "uploads/$year/05/", "uploads/$year/06/", "uploads/$year/07/",
        //     "uploads/$year/08/", "uploads/$year/08/", "uploads/$year/10/", "uploads/$year/10/", "uploads/$year/11/", "uploads/$year/12/"
        // ];

        // $path = ["uploads/2020/09/"];
        // foreach ($path as $key => $value) {
        //     echo $value . "<br>---------------------------------------------------------------------------------------<br>";
        //     $this->do_cleanup_images($value);
        // }
        //2020-10
        $this->do_cleanup_images($page);
    }

    public function do_cleanup_images($page)
    {
        ini_set('max_execution_time', '600');
        $limit = 2000;
        $offset = (($limit * $page) - $limit);
        $sql = "select a.*, b.file_name as dest_file_name
        from db_ketoday.images a
        join swalanyeti_db.images b on (a.id = b.id)
        order by b.id desc limit $limit offset $offset";
        // $sql .= " limit 1";
        echo $sql . "</br>";
        echo "Page No. " . $page . "</br>";
        $images = DB::select($sql);
        echo "-------------------------------------------------------------------------------------------------------------</br>";
        if ($images) {
            $count = 0;
            foreach ($images as $image) {
                //Generate thumbnails
                GenerateThumbnails::dispatch($image);
                $count++;
            }
            echo "Staged " . $count . "Jobs.";
        } else {
            echo "Image not found</br>";
        }
    }

    public function createThumbnail($path, $width, $height)
    {
        $img = Img::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    public function clean_inner_images($page)
    {
        ini_set('max_execution_time', '6000');
        $posts = Post::where('body', 'like', '%uploads/posts/%')->get();
        if ($posts) {
            foreach ($posts as $post) {
                CleanupInnerImages::dispatch($post);
            }
        }
    }
}
