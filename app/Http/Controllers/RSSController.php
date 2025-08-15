<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RSSController extends Controller
{
    public function index()
    {
        $posts = Post::isPublished()->with(["main_image", "category"])->orderBy('published_at', 'DESC')->skip(0)->take(30)->get();
        $this->data["title"] = "RSS Feeds - " . get_option('ak_app_title');

        $this->data['encoding'] = 'utf-8';
        $this->data['feed_name'] = route('/');
        $this->data['feed_url'] = route('rss');
        $this->data['page_description'] = 'Welcome to ' . route('/') . ' feed url page';
        $this->data['page_language'] = 'en-US';
        $this->data['creator_email'] = 'akika.digital@gmail.com';
        $this->data['posts'] = $posts;

        return response()->view('theme.' . get_option('ak_theme') . '.rss.feed', $this->data)->header('Content-Type', 'application/xml');
    }

    public function feeds()
    {
        $this->data["title"] = "RSS Feeds - " . get_option('ak_app_title');
        $this->data['categories'] = Category::isActive()->orderBy('name', 'ASC')->get();
        $this->data["title"] = "RSS Feeds - " . get_option('ak_app_title');
        return response()->view('theme.' . get_option('ak_theme') . '.rss.index', $this->data);
    }

    public function feed($slug)
    {

        $category = Category::where("slug", "=", $slug)->first();
        if (!$category) return $this->error_404($slug);
        $posts = Post::isPublished()->with(["main_image", "category"])->where("category_id", "=", $category->id)->orderBy('published_at', 'DESC')->skip(0)->take(30)->get();
        if (sizeof($posts) == 0) return $this->error_404($slug);

        $this->data = [];

        $this->data["title"] = "RSS Feeds - " . get_option('ak_app_title');
        $this->data['encoding'] = 'utf-8';
        $this->data['feed_name'] = route('/');
        $this->data['feed_url'] = route('rss');
        $this->data['page_description'] = 'Welcome to ' . route('/') . ' feed url page';
        $this->data['page_language'] = 'en-US';
        $this->data['creator_email'] = 'akika.digital@gmail.com';
        $this->data['posts'] = $posts;

        return response()->view('theme.' . get_option('ak_theme') . '.rss.feed', $this->data)->header('Content-Type', 'application/xml');
    }

    public function error_404($slug = null)
    {
        //Check if the permalink is a post before redirecting
        $post = Post::isPublished()->with(["category"])->where("slug", "=", $slug)->first();
        if ($post) {
            $permalink = get_permalink($post);
            return Redirect::to($permalink, 301);
        } else return load_theme('errors.404');
    }
}
