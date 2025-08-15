<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use App\Models\PostAuthor;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\Facades\Image as Img;

class WordpressController extends Controller
{
    public function index($module, $page)
    {
        return $this->$module($page);
    }

    public function users($page = 0)
    {
        $sql = "select a.*,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'first_name') as first_name,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'last_name') as last_name,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'description') as biography,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'twitter') as twitter,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'facebook') as facebook,
        (select meta_value from wpoc_usermeta where user_id = a.id and meta_key = 'linkedin') as linkedin
        from wpoc_users a where a.id > 1";
        $users = DB::select($sql);

        if ($users) {
            $user_id = Auth::id();
            foreach ($users as $user) {
                $data = [
                    'id' => $user->ID,
                    'first_name' => ucfirst(strtolower($user->first_name)),
                    'last_name' => ucfirst(strtolower($user->last_name)),
                    'display_name' => $user->display_name,
                    'biography' => $user->biography,
                    'email' => $user->user_email,
                    'username' => strtolower($user->user_login),
                    'password' => Hash::make("password"),
                    'user_url' => $user->user_url,
                    'facebook' => $user->facebook,
                    'twitter' => $user->twitter,
                    'linkedin' => $user->linkedin,
                    'added_by' => $user_id,
                    'updated_by' => $user_id,
                    'group_id' => 3,
                ];
                $user = User::where("id", "=", $user->ID)->first();
                if (!$user) {
                    $user = User::query()->create($data);
                    if (!$user) echo $user->display_name . " NOT Added</br>------------------------------------------------</br>";
                } else {
                    echo $user->display_name . " Already EXISTS</br>------------------------------------------------</br>";
                }
            }
        }
        echo "<br><br>DONE";
    }

    public function reset_user_passwords($i)
    {
        $users = User::get();
        foreach ($users as $user) {
            $user->password = Hash::make("47Passw@d");
            if ($user->save()) echo $user->username . " has been saved.</br>";
            else echo $user->username . " ERROR.</br>";
        }
    }

    public function categories($page = 0)
    {
        $sql = "select a.*, b.description, b.parent
        from wpoc_terms a
        join wpoc_term_taxonomy b on (a.term_id = b.term_id)
        where b.taxonomy = 'category'";
        $categories = DB::select($sql);
        if ($categories) {
            $user_id = Auth::id();
            foreach ($categories as $category) {
                $cat = Category::where("id", "=", $category->term_id)->first();
                if ($cat) {
                    $cat->name = $category->name;
                    $cat->slug = $category->slug;
                    $cat->default = ($cat->id == 1) ? '1' : '0';
                    $cat->updated_by = $user_id;
                    $cat->parent = $category->parent;
                    $cat->seo_description = $category->description;
                    try {
                        if (!$cat->save()) echo $cat->name . " NOT UPDATED</br>------------------------------------------------</br>";
                    } catch (\Throwable $th) {
                        echo $cat->name . " NOT UPDATED</br>------------------------------------------------</br>";
                    }
                } else {
                    $data = [
                        'id' => $category->term_id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'seo_keywords' => str_replace('-', ' ', $category->slug),
                        'seo_status' => '0',
                        'added_by' => $user_id,
                        'updated_by' => $user_id
                    ];
                    $category = Category::query()->create($data);
                    if (!$category) echo $category->name . " NOT Added</br>------------------------------------------------</br>";
                }
            }
        }
        echo "<br>" . sizeof($categories) . "<br>DONE";
    }

    public function tags($page = 0)
    {
        ini_set('max_execution_time', '600');
        $limit = 5000;
        $offset = (($limit * $page) - $limit);
        $sql = "select a.*
        from wpoc_terms a
        join wpoc_term_taxonomy b on (a.term_id = b.term_id)
        where b.taxonomy = 'post_tag'
        limit $limit offset $offset";

        $tags = DB::select($sql);
        if ($tags) {
            foreach ($tags as $item) {
                $tag = Tag::where("id", "=", $item->term_id)->orWhere('slug', '=', $item->slug)->first();
                if ($tag) {
                    $tag->name = substr($item->name, 0, 100);
                    $tag->seo_title = substr($item->name, 0, 100);
                    $tag->slug = substr($item->slug, 0, 100);
                    if (!$tag->save()) echo $tag->name . " - NOT UPDATED</br>------------------------------------------------</br>";
                } else {
                    $data = [
                        'id' => $item->term_id,
                        'name' => substr($item->name, 0, 100),
                        'slug' => substr($item->slug, 0, 100),
                        'seo_title' => substr($item->name, 0, 100),
                        'seo_keywords' => str_replace('-', ' ', $item->slug),
                        'seo_description' => $item->name,
                        'seo_status' => '60'
                    ];
                    $new_tag = Tag::query()->create($data);
                    if (!$new_tag) echo $new_tag->name . " - NOT Added</br>------------------------------------------------</br>";
                }
            }
        }

        echo "<br>" . sizeof($tags);
        echo "<br>DONE";
    }

    public function media($page = 0)
    {
        ini_set('max_execution_time', '600');
        $limit = 4000;
        $offset = (($limit * $page) - $limit);
        $sql = "select a.*,
        (select meta_value from wpoc_postmeta where post_id = a.ID and meta_key = '_wp_attached_file') as file_name
        from wpoc_posts a
        where a.post_type = 'attachment'
        limit $limit offset $offset";

        $images = DB::select($sql);
        if ($images) {
            foreach ($images as $item) {
                $image = Image::where("id", "=", $item->ID)->first();
                if (!$image) {

                    $data = [
                        "id" => $item->ID,
                        "file_name" => 'uploads/' . $item->file_name,
                        "slug" => $item->post_name,
                        "title" => substr($item->post_title, 0, 255),
                        "alt_text" => substr(str_replace('-', ' ', $item->post_name), 0, 255),
                        "caption" => substr($item->post_content, 0, 255),
                        "description" => substr($item->post_excerpt, 0, 255),
                        "uploaded_at" => $item->post_date,
                        "uploaded_by" => 1,
                        "updated_at" => $item->post_modified,
                        "updated_by" => 1
                    ];

                    $image = Image::query()->create($data);
                    if (!$image) echo " - NOT Added</br>------------------------------------------------</br>";
                } else {
                    echo " - SKIPPED</br>------------------------------------------------</br>";
                }
            }
        }
        echo sizeof($images) . '<br>DONE.';
    }



    public function posts($page = 0)
    {
        ini_set('max_execution_time', '600');
        $limit = 1000;
        $offset = (($limit * $page) - $limit);

        $sql = "select distinct p.*,
        (select group_concat(meta_value, ',') from wpoc_postmeta where post_id = p.ID and meta_key = 'rank_math_focus_keyword') as seo_keywords, 
        (select avg(meta_value) from wpoc_postmeta where post_id = p.ID and meta_key = 'rank_math_seo_score') as seo_status, 
        (select min(meta_value) from wpoc_postmeta where post_id = p.ID and meta_key = '_thumbnail_id') as thumbnail,
        (select min(meta_value) from wpoc_postmeta where post_id = p.ID and meta_key = 'gossip_main_image') as gossip_thumbnail,
        (select sum(meta_value) from wpoc_postmeta where post_id = p.ID and meta_key = '_thumbnail_id') as views,
        (select meta_value from wpoc_postmeta where post_id = p.ID and meta_key = 'gossip_description') as gossip_description,
        (SELECT min(t.term_id)
        FROM wpoc_terms t
            LEFT JOIN wpoc_term_taxonomy tt ON t.term_id = tt.term_id
            LEFT JOIN wpoc_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE tt.taxonomy = 'category' AND p.ID = tr.object_id
        ) AS category_id,
        (SELECT group_concat(t.term_id SEPARATOR ', ')
        FROM wpoc_terms t
            LEFT JOIN wpoc_term_taxonomy tt ON t.term_id = tt.term_id
            LEFT JOIN wpoc_term_relationships tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE tt.taxonomy = 'post_tag' AND p.ID = tr.object_id
        ) AS tags
        FROM `wpoc_posts` AS p
        LEFT JOIN `wpoc_term_relationships` AS tr ON (p.ID = tr.object_id)
        LEFT JOIN  `wpoc_term_taxonomy` AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
        WHERE post_type in ('gossips', 'posts') AND post_status='publish'
        ORDER BY p.ID DESC
        limit $limit offset $offset";

        $posts = DB::select($sql);


        if ($posts) {
            foreach ($posts as $post) {
                $body = (strlen($post->post_content) > 10) ? $post->post_content : $post->gossip_description;
                $body = $this->cleanup_post_body($body);
                $body = $this->clean_image($body);
                $img = (strlen($post->thumbnail) > 0) ? $post->thumbnail : $post->gossip_thumbnail;
                $featured_image = Image::where("id", "=", $img)->first();
                $data = [
                    "id" => $post->ID,
                    "title" => substr($post->post_title, 0, 255),
                    "slug" => $post->post_name,
                    "body" => $body,
                    "excerpt" => substr($post->post_excerpt, 0, 255) ?? substr($post->post_title, 0, 255),
                    "category_id" => $post->category_id ?? 1,
                    "seo_keywords" => $post->seo_keywords,
                    "seo_title" => substr($post->post_title, 0, 255),
                    "seo_description" => substr($post->post_title, 0, 255),
                    "seo_status" => intval($post->seo_status) ?? "0",
                    "is_breaking" => "0",
                    "is_featured" => "0",
                    "is_sponsored" => "0",
                    "display_ads" => "1",
                    "featured_image" => ($featured_image) ? $featured_image->id : NULL,
                    "post_label" => $post->category_name ?? NULL,
                    "homepage_ordering" => "0",
                    "created_by" => ($post->post_author == 84) ? 1 : $post->post_author,
                    "current_editor" => NULL,
                    "status_id" => 3,
                    "total_views" => $post->views ?? 0,
                    "published_at" => $post->post_modified,
                    "published_by" => ($post->post_author == 84) ? 1 : $post->post_author,
                ];

                $this_post = Post::where("id", "=", $post->ID)->first();
                if ($this_post) {
                    $this_post->body = $this->cleanup_post_body($post->post_content);
                    $this_post->save();
                } else {
                    try {
                        $new_post = Post::query()->create($data);
                        if ($new_post) {
                            //Post Authors
                            PostAuthor::query()->create([
                                "post_id" => $new_post->id,
                                "author_id" => ($post->post_author == 84) ? 1 : $post->post_author
                            ]);

                            //Post Tags
                            if ($post->tags != NULL) {
                                $tags = explode(", ", $post->tags);
                                for ($i = 0; $i < sizeof($tags); $i++) {
                                    PostTag::query()->create([
                                        "post_id" => $new_post->id,
                                        "tag_id" => $tags[$i]
                                    ]);
                                }
                            }
                        }

                        //Update post as fetched
                        DB::table('wpoc_posts')
                            ->where('ID', $post->ID)  // find your user by their email
                            ->limit(1)  // optional - to ensure only one record is updated.
                            ->update(array('processed' => "1"));
                    } catch (\Throwable $th) {
                        DB::table('wpoc_posts')
                            ->where('ID', $post->ID)  // find your user by their email
                            ->limit(1)  // optional - to ensure only one record is updated.
                            ->update(array('processed' => "9", "message" => $th->getMessage()));
                        continue;
                    }
                }
            }
        }
        echo sizeof($posts) . ' Posts Added';
    }

    private function cleanup_post_body($text)
    {
        $text = str_replace('<!-- wp:paragraph -->', '', $text);
        $text = str_replace('<!-- /wp:paragraph -->', '', $text);

        $paragraphs = preg_split('/\n+/', $text);
        $body = "";
        foreach ($paragraphs as $p) {
            if (strlen($p) > 1) {
                if (strpos($p, '<figure') !== false) {
                    $original_p = $p;
                    $p = str_replace("https://tv47.co.ke/wp-content/", "https://www.nairobigossipclub.co.ke/storage/", $p);
                    info($p);
                    preg_match('/<img[^>]+>/i', $p, $p);
                    $caption = "";
                    $image = "";
                    try {
                        $image = $this->clean_image($p[0]);
                        $caption = explode("/>", str_replace("[/caption]", "", $original_p))[1];
                    } catch (\Throwable $th) {
                        $caption = explode(">", str_replace("[/caption]", "", $original_p))[1];
                    }
                    $caption = str_replace("\r", "", $caption);
                    if ($image) $p = '<figure class="wwt-intext-image" style="width: 1080px;">' . $image . '<figcaption class="wwt-intext-image-caption">' . $caption . '</figcaption></figure>';
                } else if (strpos($p, 'https://twitter.com/') !== false) {
                    $p = strip_tags($p);
                    $p = '<blockquote class="twitter-tweet"><a href="' . $p . '"></a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
                } else if (strpos($p, 'https://www.facebook.com/') === true) {
                    $p = strip_tags($p);
                    $p = '<div class="fb-post" data-href="' . $p . '" data-width="500" data-show-text="true"><blockquote cite="' . $p . '" class="fb-xfbml-parse-ignore">Posted by <a href="https://www.facebook.com/facebookapp/">Facebook App</a> on&nbsp;<a href="' . $p . '">Thursday, August 27, 2015</a></blockquote></div>';
                } else if (preg_match('/^https:\/\/www\.instagram\.com\/p\/.{6,}\?/', $p)) {
                    $p = strip_tags($p);
                    $code = explode("/", $p)[4];
                    $p = '<iframe src="//www.instagram.com/p/CZ7cntdDUpM/embed"></iframe>';
                } else if (strpos($p, 'https://www.youtube.com/watch') !== false) {
                    $p = strip_tags($p);
                    $array = explode("https://www.youtube.com/watch?v=", $p);
                    if (sizeof($array) > 1) {
                        $code = $array[1];
                        $p = '<iframe src="https://www.youtube.com/embed/' . $code . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    }
                } else if (strpos($p, 'https://youtu.be/') !== false) {
                    $p = strip_tags($p);
                    $array = explode("https://youtu.be/", $p);
                    if (sizeof($array) > 1) {
                        $code = $array[1];
                        $p = '<iframe src="https://www.youtube.com/embed/' . $code . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    }
                }

                $body = str_replace("https://tv47.co.ke/wp-content/", "https://www.nairobigossipclub.co.ke/storage/", $body);
                if ($p) $body .= "<p>" . $p . "</p>";
            }
        }
        return $body;
    }

    private function clean_image($image)
    {
        try {
            $doc = new DOMDocument();
            $doc->loadHTML($image);
            $xpath = new DOMXPath($doc);
            $src = $xpath->evaluate("string(//img/@src)");
            $src = str_replace("https://www.nairobigossipclub.co.ke/storage/uploads/", "", $src);
            $src_array = explode("/", $src);

            $size = sizeof($src_array);
            $size--;
            $file_name = $src_array[$size];

            $file_arr = explode(".", $file_name);
            $name = $file_arr[0];

            $name_arr = explode("-", $name);
            $size = sizeof($name_arr);
            $size--;

            $size_def = "-" . $name_arr[$size];

            $image = str_replace($size_def, "", $image);

            info($image);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $image;
    }

    public function cleanup_posts($slug)
    {
        $posts = Post::where("body", "like", "%<figure%")->get();
        if ($posts) {
            foreach ($posts as $post) {
                try {
                    $post->body = $this->cleanup_post_body($post->body);
                    if ($post->save()) {
                        echo $post->id . " UPDATED</br>";
                    } else {
                        echo $post->id . " NOT UPDATED</br>";
                    }
                } catch (\Throwable $th) {
                    echo $post->id . " NOT UPDATED</br>";
                }
            }
        }
    }

    public function cleanup_images()
    {
        ini_set('max_execution_time', '600');
        // $year = "2022";
        // $path = [
        //     "uploads/$year/01/", "uploads/$year/02/", "uploads/$year/03/", "uploads/$year/04/", "uploads/$year/05/", "uploads/$year/06/", "uploads/$year/07/",
        //     "uploads/$year/08/", "uploads/$year/09/", "uploads/$year/10/", "uploads/$year/11/", "uploads/$year/12/"
        // ];
        $path = ["uploads/2022/10/"];
        foreach ($path as $key => $value) {
            echo $value . "<br>---------------------------------------------------------------------------------------<br>";
            $this->do_cleanup_images($value);
        }
    }

    public function do_cleanup_images($path)
    {
        ini_set('max_execution_time', '600');
        $images = Image::where("file_name", "like", '%' . $path . '%')->get();
        if ($images) {
            $count = 1;
            foreach ($images as $image) {
                //Generate thumbnails
                echo $count . ". | " . $image->file_name . " ";
                $file_name = $image->file_name;
                $name_arr = explode('.', $file_name);

                $src = public_path('storage/' . $file_name);
                $dest = public_path('storage/new/' . $file_name);

                $base_path = pathinfo($src, PATHINFO_DIRNAME);
                $filename = pathinfo($src, PATHINFO_FILENAME); //get filename without extension
                $extension = pathinfo($src, PATHINFO_EXTENSION); //get filename without extension

                try {
                    $copy = File::copy($src, $dest);

                    if (!$copy) echo "File not copied</br>";

                    $thumbnails = get_thumbnail_sizes();
                    if ($thumbnails) {
                        foreach ($thumbnails as $key => $value) {
                            $name_array = explode(".", $image->file_name);
                            $thumbnail = $filename . '-' . $key . '.' . $extension;

                            $dest = public_path('storage/new/' . $path . $thumbnail);
                            $copy = File::copy($src, $dest);

                            try {
                                $this->createThumbnail($dest, $value, $value);
                            } catch (Exception $e) {
                                info($e->getMessage());
                            }
                        }
                    }

                    echo "File COPIED</br>";
                } catch (\Throwable $th) {
                    echo $th->getMessage() . " - Error Ocurred</br>";
                }
                $count++;
            }
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
}
