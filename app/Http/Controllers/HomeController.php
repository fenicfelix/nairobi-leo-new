<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DailyPageviews;
use App\Models\ecommerce\Payment;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index1()
    {
        echo json_encode(get_thumbnail_sizes());
    }
    public function index()
    {
        session()->forget('exclude');
        if (get_option('ak_sticky_posts') > 0) $this->data["pinned_posts"] = Post::isPublished()->with(["main_image", "category", "authors"])->where("homepage_ordering", "!=", "0")->orderBy('homepage_ordering', 'ASC')->get();
        return load_theme('homepage', $this->data);
    }

    public function category($slug)
    {
        session()->forget('exclude');
        $category = Category::where("slug", "=", $slug)->first();
        if (!$category) return $this->error_404($slug);
        $posts = Post::isPublished()->with(["main_image", "category", "authors"])->where("category_id", "=", $category->id)->orderBy('published_at', 'DESC')->skip(0)->take(30)->get();
        if (sizeof($posts) == 0) return $this->error_404($slug, "Posts Not Found", "No posts found that matched your search.");

        $this->data["keywords"] = $category->seo_keywords;
        $description = $this->data["description"];
        $this->data["description"] = $category->seo_description  ?? $description;

        $this->data["title"] = $category->name . " - " . get_option('ak_app_title');
        $this->data["category"] = $category;
        $this->data["posts"] = $posts;
        return load_theme('category', $this->data);
    }

    public function preview($id)
    {
        $post = Post::with(["authors", "tags", "category", "main_image"])->where("id", "=", $id)->first();
        // unhide body column
        $post->makeVisible('body');
        $this->data["post"] = $post;

        $this->data["keywords"] = $post->seo_keywords;
        $description = $this->data["description"];
        $this->data["description"] = $post->seo_description  ?? $description;

        $this->data["more_stories"] = Post::isPublished()->with(["main_image"])->where("id", "!=", $post->id)->orderBy("published_at", "desc")->skip(0)->take(6)->get();
        $this->data["latest_stories"] = Post::isPublished()->with(["main_image"])->where("id", "!=", $post->id)->orderBy("published_at", "desc")->skip(0)->take(6)->get();
        $this->data["global_post"] = $post;
        return load_theme('single', $this->data);
    }

    public function old_post($slug, Request $request)
    {
        return $this->error_404($slug);
    }

    public function old_ci_post($category, $postId, $slug)
    {
        return $this->error_404($slug);
    }

    public function post($category, $id, $slug, Request $request)
    {
        $post = Post::isPublished()->with(["authors", "tags", "category", "main_image"])->where("id", "=", $id)->first();
        if (!$post) return $this->error_404($slug);

        //Permanently redirect in case the category does not match the url category
        if (!page_is($post->category->slug)) return $this->error_404($slug);

        $this->data["global_post"] = $post;
        return $this->fetch_post_data($post);
    }

    private function fetch_post_data($post)
    {
        $this->update_page_views($post);
        $this->data["title"] = $post->title ?? $post->seo_title;

        $this->data["keywords"] = $post->seo_keywords ?? $this->data["keywords"];
        $this->data["description"] = $post->seo_description ?? $post->seo_title;

        if ($post->main_image) $this->data["image"] = Storage::disk('public')->url($post->main_image->file_name);
        $this->data["pubdate"] = date("Y-m-d H:i:sP", strtotime($post->published_at));
        $this->data["post"] = $post;
        $this->data["guest"] = Auth::guest() ? true : false;

        return load_theme('single', $this->data);
    }

    public function search(Request $request)
    {
        $this->data["request"] = $request;
        $this->data["title"] = "Search Results";

        $this->data["keywords"] = $request->get('query');

        $posts = Post::isPublished()->orderBy('published_at', 'DESC')->with(["main_image"])->where("title", "like", "%" . $request->get('query') . "%")->orWhere("body", "like", "%" . $request->get('query') . "%")->skip(0)->take(20)->get();

        $this->data["title"] = "Search: " . $request->get('query') . " - " . get_option('ak_app_title');
        $this->data["posts"] = $posts;
        return load_theme('search', $this->data);
    }

    public function tags($topic)
    {
        $this->data["topic"] = $topic;
        $tag = Tag::where("slug", "=", $topic)->first();
        if (!$tag) return $this->error_404($topic);
        $posts = $tag->posts()->isPublished()->orderBy('published_at', 'DESC')->skip(0)->take(20)->get();

        $this->data["keywords"] = $tag->name;
        $description = $this->data["description"];
        $this->data["description"] = $tag->seo_description ?? $description;

        $title = "Topic : " . ($tag->seo_title ?? $tag->name) . " - " . get_option('ak_app_title');
        $this->data["title"] = $title;
        $this->data["tag"] = $tag;
        $this->data["posts"] = $posts;
        return load_theme('tags', $this->data);
    }

    public function author($author)
    {
        $user = User::where("username", "=", $author)->first();
        if (!$user) return $this->error_404($author);

        $posts = $user->posts()->isPublished()->orderBy('published_at', 'DESC')->skip(0)->take(20)->get();

        $this->data["keywords"] = $user->display_name;
        $description = $this->data["description"];
        $this->data["description"] = $user->biography ?? $description;

        $this->data["title"] = $user->display_name . ", author at " . get_option('ak_app_title');
        $this->data["user"] = $user;
        $this->data["posts"] = $posts;
        return load_theme('author', $this->data);
    }

    private function update_page_views(Post $post)
    {
        $post->increment('total_views');

        $todaysViews = DailyPageviews::where('date', date('Y-m-d'))->first();
        if ($todaysViews) $todaysViews->increment('total');
        else {
            DailyPageviews::create([
                'date' => date('Y-m-d')
            ]);
        }
    }

    public function publish_scheduled_posts()
    {
        $time = date('Y-m-d H:i:s');
        $posts = Post::isScheduled()->where("published_at", ">=", $time)->get();
        if ($posts) {
            foreach ($posts as $post) {
                $post->status_id = 3;
                $post->save();

                if ($post->homepage_ordering != "0") clear_homepage_ordering($post->id, $post->homepage_ordering);
                if ($post->is_breaking) clear_breaking_stories($post->id);
            }
        }
    }

    public function page($slug)
    {
        $page = Page::where("slug", "=", $slug)->first();
        if (!$page) return $this->error_404($slug);

        $this->data["page"] = $page;
        $this->data["title"] = $page->seo_title ?? $page->title . " - " . get_option('ak_app_title');
        $description = $this->data["description"];
        $this->data["description"] = $page->seo_description ?? $description;
        return load_theme('pages', $this->data);
    }

    public function error_404($slug = null, $title = null, $description = null)
    {
        //Check if the permalink is a post before redirecting
        $post = Post::isPublished()->with(["category"])->where("slug", "=", $slug)->first();
        if ($post) {
            $permalink = get_permalink($post);
            return Redirect::to($permalink, 301);
        } else {
            $this->data["title"] = $title;
            $this->data["description"] = $description;
            return load_theme('errors.404', $this->data);
        }
    }

    public function more_stories(Request $request, $type, $value)
    {
        $limit = get_option("ak_load_more_limit");
        $offset = ($limit * $request->get("page"));
        if ($type == "category") {
            $category = Category::where('slug', $value)->first();
            if (!$category) return response()->json(['status' => status_success, 'data' => ""], Response::HTTP_OK);
            $posts = $category->posts()->isPublished()->with(["main_image", "category", "authors"])->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
        } else if ($type == "search") {
            $posts = Post::isPublished()->with(["main_image", "category", "authors"])->where("title", "like", "%" . $value . "%")->orWhere("body", "like", "%" . $value . "%")->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
        } else if ($type == "tag") {
            $tag = Tag::where("slug", "=", $value)->first();
            if (!$tag) return response()->json(['status' => status_success, 'data' => ""], Response::HTTP_OK);
            $posts = $tag->posts()->isPublished()->with(["main_image", "category", "authors"])->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
        } else if ($type == "author") {
            $user = User::where("username", "=", $value)->first();
            if (!$user) return $this->error_404($value);

            $posts = $user->posts()->isPublished()->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
        } else {
            //Homepage
            $posts = fetch_latest_excluded_posts($limit, $offset);
        }
        if (!$posts) {
            return response()->json(['status' => status_success, "load_more_url" => "", 'data' => []], Response::HTTP_OK);
        }

        $html = "";
        $counter = 1;
        foreach ($posts as $post) {
            $html .= load_theme('templates.load-more', ['post' => $post, "counter" => $counter]); // template_simple_list($post, true);
            $counter++;
        }

        if ($html == "") return response()->json(['status' => status_success, "load_more_url" => "", 'data' => []], Response::HTTP_OK);

        return response()->json(['status' => status_success, 'data' => $html], Response::HTTP_OK);
    }
}
