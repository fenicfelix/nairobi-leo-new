<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function index()
    {
        return load_sitemap_template('index', $this->data);
    }

    public function posts($page = null)
    {
        $limit = 200;
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

   public function newsOld($page = 1, $limit = 10) // You may decide the default values
   {
    $skip = ($page - 1) * $limit;
    if ($skip < 0) $skip = 0;

    $news = Post::isPublished()
            ->where('published_at', '>=', Carbon::now()->subDays(2)) // Get news from the last 2 days
            ->with(["main_image", "category"])
            ->orderBy('published_at', 'DESC')
            ->skip($skip)
            ->take($limit)
            ->get();

     return $news;   // not a test String
   }

public function newsv1($page = 1, $limit = 20) {
    $skip = ($page - 1) * $limit;
    if ($skip < 0) $skip = 0;
      
    $newsPosts = Post::isPublished()
        ->where('published_at', '>=', Carbon::now()->subDays(2)) // Get news from the last 2 days
        ->with(["main_image", "category"])
        ->orderBy('published_at', 'DESC')
        ->skip($skip)
        ->take($limit)
        ->get();

    $xml = new \SimpleXMLElement('<urlset xmlns="'. 'http://www.sitemaps.org/schemas/sitemap/0.9' .'" xmlns:news="'. 'http://www.google.com/schemas/sitemap-news/0.9' .'"></urlset>');
    
    foreach ($newsPosts as $newsPost) {
        $newsXml = $xml->addChild('url');
        $newsXml->addChild('loc', 'https://nairobileo.co.ke/'.$newsPost->slug);
        $newsInfo = $newsXml->addChild('news:news');
        $newsPublication = $newsInfo->addChild('news:publication');
        $newsPublication->addChild('news:name', "NairobiLeo"); // Replace with your publication name
        $newsPublication->addChild('news:language', 'en');
        $newsInfo->addChild('news:publication_date', Carbon::parse($newsPost->published_at)->format('Y-m-d').'T'.Carbon::parse($newsPost->published_at)->format('H:i:s').'-00:00');
        $newsInfo->addChild('news:title', $newsPost->title);
    }

    return Response::make($xml->asXML(), '200')->header('Content-Type', 'application/xml');
}

public function news($page = 1, $limit = 30)
{
    $skip = ($page - 1) * $limit;
    if ($skip < 0) $skip = 0;
    
    $newsPosts = Post::isPublished()
        ->where('published_at', '>=', Carbon::now()->subDays(2))
        ->with(["main_image", "category"])
        ->orderBy('published_at', 'DESC')
        ->skip($skip)
        ->take($limit)
        ->get();

    $xml = new \SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" />');
  
    foreach ($newsPosts as $newsPost) {
        $url = $xml->addChild('url');
        $url->addChild('loc', 'https://nairobileo.co.ke/'.$newsPost->slug);
        $news = $url->addChild('news:news', null, 'http://www.google.com/schemas/sitemap-news/0.9');
        $publication = $news->addChild('news:publication', null, 'http://www.google.com/schemas/sitemap-news/0.9');
        $publication->addChild('news:name', "Nairobi Leo", 'http://www.google.com/schemas/sitemap-news/0.9');
        $publication->addChild('news:language', 'en', 'http://www.google.com/schemas/sitemap-news/0.9');
        $news->addChild('news:publication_date', Carbon::parse($newsPost->published_at)->format('Y-m-d').'T'.Carbon::parse($newsPost->published_at)->format('H:i:s').'-00:00', 'http://www.google.com/schemas/sitemap-news/0.9');
        $news->addChild('news:title', htmlspecialchars($newsPost->title), 'http://www.google.com/schemas/sitemap-news/0.9');
    }

    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    return Response::make($dom->saveXML(), '200')->header('Content-Type', 'application/xml');
}

}
