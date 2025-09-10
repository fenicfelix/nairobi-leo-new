<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::isPublished()
            ->with(["main_image", "category"])
            ->where('published_at', '>=', now()->subDays(2))
            ->orderBy('published_at', 'DESC')
            ->get();
        $this->data["posts"] = $posts;
        return load_sitemap_template('index', $this->data);
    }

    public function googleNewsSitemap()
    {
        $posts = Post::isPublished()
            ->with(["main_image", "category"])
            ->orderBy('published_at', 'DESC')
            ->skip(0) // Start from the first post
            ->take(200) // Limit to 1000 posts
            ->get();
        $this->data["posts"] = $posts;
        return load_sitemap_template('google_news_sitemap', $this->data);
    }

    public function posts($page = null)
    {
        $limit = 1000;
        if ($page) {
            $this->data["posts"] = Post::isPublished()->with(["main_image", "category"])->orderBy('published_at', 'DESC')->skip(($page - 1) * $limit)->take($limit)->get();
            return load_sitemap_template('posts', $this->data);
        } else {
            $total_posts = Post::select('id')->isPublished()->count();
            $this->data['pages'] = ceil($total_posts / $limit);
            return load_sitemap_template('posts_index', $this->data);
        }
    }

    public function tags()
    {
        $this->data["tags"] = Tag::orderBy('id', 'DESC')->get();
        return load_sitemap_template('tags', $this->data);
    }

    public function categories()
    {
        $this->data["categories"] = Category::orderBy('id', 'DESC')->get();
        return load_sitemap_template('categories', $this->data);
    }

    public function authors()
    {
        $this->data["authors"] = User::where("id", ">", 1)->orderBy('id', 'DESC')->get();
        return load_sitemap_template('authors', $this->data);
    }

    public function pages()
    {
        $this->data["pages"] = Page::orderBy('id', 'DESC')->get();
        return load_sitemap_template('pages', $this->data);
    }
}
