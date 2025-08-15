<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\SystemPreference;
use App\Models\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('generate_identifier')) {
    function generate_identifier(): string
    {
        return Str::uuid()->toString();
    }
}

if (!function_exists('get_seo_status')) {
    function get_seo_status($status): string
    {
        if ($status >= 80) return '<i class="fas fa-circle text-success"></i>';
        else if ($status >= 60) return '<i class="fas fa-circle text-warning"></i>';
        else return '<i class="fas fa-circle text-danger"></i>';
    }
}

if (!function_exists('get_template_url')) {
    function get_template_url()
    {
        return 'theme/frontend/assets/';
    }
}

if (!function_exists('get_option')) {
    function get_option($key)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $option = SystemPreference::where("slug", "=", $key)->first();
            if ($option) {
                Cache::put($key, $option->value, 3600);
                return $option->value;
            } else {
                return '';
            }
        }
    }
}

if (!function_exists('get_widget')) {
    function get_widget($slug)
    {
        $widget = Widget::where("slug", $slug)->first();
        if ($widget) return $widget->body;

        return NULL;
    }
}

if (!function_exists('load_theme')) {
    function load_theme($page, $data = [])
    {
        return view("theme." . get_option("ak_theme") . "." . $page, $data);
    }
}

if (!function_exists('load_template')) {
    function load_template($page)
    {
        return "theme." . get_option("ak_theme") . ".templates." . $page;
    }
}

if (!function_exists('load_sitemap_template')) {
    function load_sitemap_template($page, $data = [])
    {
        return response()->view('theme.' . get_option("ak_theme") . ".sitemaps." . $page, $data)->header('Content-Type', 'text/xml');
    }
}

if (!function_exists('replace_cache_option')) {
    function replace_cache_option($key, $value)
    {
        if (Cache::has($key)) {
            Cache::put($key, $value, 3600);
        } else {
            Cache::put($key, $value, 3600);
        }
    }
}

function clear_homepage_ordering($postId, $position)
{
    return DB::table("posts")
        ->where("id", "!=", $postId)
        ->where("status_id", "=", "3")
        ->where("homepage_ordering", "=", $position)
        ->update(["homepage_ordering" => "0"]);
}

function getTimeAgo($time)
{
    $dt     = Carbon::now();
    $time = Carbon::parse($time);

    return str_ireplace(
        [' seconds', ' second', ' minutes', ' minute', ' hours', ' hour', ' days', ' day', ' weeks', ' week', 'before'],
        [' secs', ' sec', ' mins', ' min', ' hrs', ' hr', ' days', ' day', ' weeks', ' week', 'ago'],
        $time->diffForHumans($dt)
    );
}

function get_permalink($post)
{
    try {
        $permalink = route('post', [$post->category->slug, $post->id, $post->slug]);
    } catch (\Throwable $th) {
        info("PERMALINK_ERROR: " . $post->id . " | " . $th->getMessage());
        $permalink = "#";
    }
    return $permalink;
}

function get_time_ago($post)
{
    return getTimeAgo($post->published_at);
}

function get_authors($post)
{
    try {
        return $post->authors[0]->display_name;
    } catch (\Throwable $th) {
        info("get_authors_error: " . $post->title . " | " . $th->getMessage());
        return "Anonymous";
    }
}

function clear_breaking_stories($postId)
{
    return DB::table("posts")
        ->where("id", "!=", $postId)
        ->where("status_id", "=", "3")
        ->where("is_breaking", "=", "1")
        ->update(["is_breaking" => "0"]);
}

function fetch_settings($slug)
{
    $setting = SystemPreference::where("slug", "=", $slug)->first();
    if ($setting) return $setting->value;
    return false;
}

function fetch_category_from_slug($slug)
{
    $category = Category::where("slug", "=", $slug)->first();
    return $category;
}

function get_breaking_news($global_post)
{
    $breaking_news = Post::isPublished()->isBreaking()->with(["category"])->orderBy('published_at', 'DESC');
    if ($global_post) $breaking_news = $breaking_news->whereNotIn('id', [$global_post->id]);
    $breaking_news = $breaking_news->first();
    if ($breaking_news) {
        if (!Cookie::get("post-" . $breaking_news->id)) {
            return $breaking_news;
        }
    }

    return false;
}

function fetch_posts_by_category($categoryId, $limit, $offset = 0)
{
    $exclude = session()->get('exclude') ?? [];
    $posts = Post::isPublished()->with(["main_image", "category", "authors"])->where("category_id", "=", $categoryId)->whereNotIn('id', $exclude)->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
    return $posts;
}

function fetch_posts_by_category_slug($slug, $limit, $offset = 0)
{
    $exclude = session()->get('exclude') ?? [];
    $category = Category::where('slug', $slug)->first();
    if (!$category) return false;

    $posts = Post::isPublished()->with(["main_image", "category", "authors"])->where("category_id", "=", $category->id)->whereNotIn('id', $exclude)->orderBy('published_at', 'DESC');
    if ($limit > 0) $posts = $posts->skip($offset)->take($limit);
    return
        $posts->get();
}

function fetch_ecommerce_products_by_category_slug($slug, $limit, $offset = 0)
{
    $exclude = session()->get('exclude') ?? [];
    $category = Category::where('slug', $slug)->first();
    if (!$category) return false;

    $posts = Post::isPublished()->with(["main_image", "category", "authors", "product"])->where("category_id", "=", $category->id)->whereNotIn('id', $exclude)->orderBy('homepage_ordering', 'ASC');
    if ($limit > 0) $posts = $posts->skip($offset)->take($limit);
    return
        $posts->get();
}

function unset_current_editor($postId = NULL)
{
    $unset = false;
    if (is_null($postId)) $unset = Post::where("current_editor", "=", Auth::id())->update(["current_editor" => NULL]);
    else $unset = Post::where("id", "=", $postId)->update(["current_editor" => NULL]);

    if ($unset) return true;

    return false;
}

function set_current_editor($postId)
{
    $set = Post::where("id", "=", $postId)->update(["current_editor" => Auth::id()]);
    if (!$set) return false;

    return true;
}

function can_edit_post($user)
{
    if ($user && in_array($user->group_id, [1, 2])) return true;

    return false;
}

function is_mobile()
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        return true;
    }

    return false;
}

function is_homepage()
{
    return request()->is('/')  ? true : false;
}

function is_category_page()
{
    return (request()->is('*/category/*')) ? true : false;
}

function is_single_post_page()
{
    return (request()->is('*/article/*')) ? true : false;
}

function page_is($slug)
{
    return (request()->is('*' . $slug . '/*')) ? true : false;
}

function fetch_related_posts($post, $limit = 3)
{
    if (!$post->tags) {
        return false;
    }

    $tag_ids = [];
    foreach ($post->tags as $tag) {
        array_push($tag_ids, $tag->id);
    }

    $posts = Post::isPublished()->with(["category"])
        ->where("posts.id", "!=", $post->id)
        ->join('post_tags', function ($join) use ($tag_ids) {
            $join->on('posts.id', '=', 'post_tags.post_id')
                ->whereIn('post_tags.tag_id', $tag_ids);
        })->orderBy('published_at', 'DESC')
        ->skip(0)->take($limit)->select('posts.id', 'posts.title', 'posts.slug', 'posts.published_at', 'posts.category_id')->distinct()->get();

    return $posts;
}

function social_share($title, $permalink)
{
    return view('theme.' . get_option('ak_theme') . '.templates.social-share', ['title' => $title, 'permalink' => $permalink]);
}

function featured_posts($limit = 5)
{
    return Post::isPublished()->isfeatured()->with("main_image")->orderBy("published_at", "desc")->skip(0)->take($limit)->get();
}

function latest_stories($limit)
{
    return Post::isPublished()->with(["main_image", "category"])->orderBy('published_at', 'DESC')->skip(0)->take($limit)->get();
}

function trending_posts($limit)
{
    $date = date('Y-m-d H:i:s', strtotime('- ' . get_option('ak_trending_validity') . ' hours'));
    return Post::isPublished()->with("main_image")->where('published_at', ">", $date)->orderBy("total_views", "desc")->skip(0)->take($limit)->get();
}

function fetch_image($file_url, $file_size)
{
    $file_url = $file_url ?? get_option('ak_alt_image');
    if ($file_size == "original") return Storage::disk('public')->url($file_url);

    $image_array = explode(".", $file_url);
    $size = sizeof($image_array);
    $size--;
    $ext = $image_array[$size];

    $file_name = str_replace("." . $ext, "", $file_url);

    $path =
        Storage::disk('public')->url($file_name . config('cms.thumbnail_separator') . $file_size . "." . $ext);

    return (file_exists($path)) ? $path :
        Storage::disk('public')->url($file_url);
}

function delete_image($file_url, $file_size)
{
    $file_url = $file_url ?? get_option('ak_alt_image');
    if ($file_size == "original") {
        if (!Storage::disk('public')->exists($file_url)) return true;
        if (Storage::disk('public')->delete($file_url)) {
            return true;
        }
        return false;
    }

    $image_array = explode(".", $file_url);
    $size = sizeof($image_array);
    $size--;
    $ext = $image_array[$size];

    $file_name = str_replace("." . $ext, "", $file_url);

    $file_path = $file_name . config('cms.thumbnail_separator') . $file_size . "." . $ext;

    if (!Storage::disk('public')->exists($file_path)) return true;

    if (Storage::disk('public')->delete($file_path)) {
        return true;
    }

    return false;
}

function reset_exclude()
{
    session()->forget('exclude');
}

function exclude_post($post)
{
    $exclude = session()->get('exclude') ?? [];
    array_push($exclude, $post->id);
    if (sizeof($exclude) > 0) session(["exclude" => $exclude]);
}

function fetch_random_posts($category, $offset, $limit)
{
    if ($category) return Post::isPublished()->with(["main_image", "category", "authors"])->where("category_id", "=", $category->id)->orderByRaw('RAND()')->skip($offset)->take($limit)->get();
    else return Post::isPublished()->with(["main_image", "category", "authors"])->orderByRaw('RAND()')->skip($offset)->take($limit)->get();
}

function fetch_latest_excluded_posts($limit, $offset)
{
    $exclude = session()->get('exclude') ?? [];
    if (sizeof($exclude) > 0) $posts = Post::isPublished()->with(["main_image", "category"])->whereNotIn('id', session()->get('exclude'))->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();
    else $posts = Post::isPublished()->with(["main_image", "category"])->where("homepage_ordering", "0")->orderBy('published_at', 'DESC')->skip($offset)->take($limit)->get();

    return $posts;
}

function can_publish($logged_user)
{
    return (in_array($logged_user->group_id, [1, 2])) ? true : false;
}

function xml_convert($str, $protect_all = FALSE)
{
    $temp = '__TEMP_AMPERSANDS__';

    // Replace entities to temporary markers so that
    // ampersands won't get messed up
    $str = preg_replace('/&#(\d+);/', $temp . '\\1;', $str);

    if ($protect_all === TRUE) {
        $str = preg_replace('/&(\w+);/', $temp . '\\1;', $str);
    }

    $str = str_replace(
        array('&', '<', '>', '"', "'", '-'),
        array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'),
        $str
    );

    // Decode the temp markers back to entities
    $str = preg_replace('/' . $temp . '(\d+);/', '&#\\1;', $str);

    if ($protect_all === TRUE) {
        return preg_replace('/' . $temp . '(\w+);/', '&\\1;', $str);
    }

    return $str;
}

function get_thumbnail_sizes()
{
    $result = [];
    $thumbnail_sizes = get_option('ak_thumbnail_sizes');
    if ($thumbnail_sizes) {
        $thumbnail_sizes = explode(",", $thumbnail_sizes);
        sort($thumbnail_sizes);
        $result = [
            "sm" => $thumbnail_sizes[0],
            "md" => $thumbnail_sizes[1],
            "lg" => $thumbnail_sizes[2],
        ];
    }

    return $result;
}

function get_menu($data)
{
    $data = (object) $data;
    $html = "";
    if ($data->container) {
        $container = explode("%%", $data->container);
        $html = $container[0];
    }
    if ($data->menu_wrapper) {
        $menu_wrapper = explode("%%", $data->menu_wrapper);
        $html .= $menu_wrapper[0];
    }

    $menu = Menu::where("slug", $data->slug)->first();
    if ($menu) {
        $menuItems = MenuItem::where('menu_id', $menu->id)->orderBy('order', 'ASC')->get();
        if ($menuItems) {
            $i = 1;
            foreach ($menuItems as $menuItem) {
                $active = ((str_contains(url()->full(), "/" . $menuItem->slug)) || (url()->full() == "/" . $menuItem->url)) ? 'active' : '';
                $link_wrap = explode("%%", $data->menu_item_wrapper);
                $link_wrap[0] = str_replace('active', $active, $link_wrap[0]);
                $html .= str_replace('active', $active, $link_wrap[0]);
                $html .= '<a class="' . $data->link_class . ' ' . $active . '" href="' . get_menu_item_link($menuItem) . '">' . $menuItem->display_title . '</a>';
                $html .= $link_wrap[1];
                if (isset($data->separator) && $i < sizeof($menuItems)) $html .= $data->separator;
                $i++;
            }
        }
    }

    if ($data->menu_wrapper) {
        $html .= $menu_wrapper[1];
    }
    if ($data->container) $html .= $container[1];
    echo $html;
}

function get_menu_item_link($menuItem)
{
    $url = "#";
    switch ($menuItem->type) {
        case 'page':
            $page = Page::where("id", $menuItem->reference_id)->first();
            if ($page) $url = route('page', $page->slug);
            break;

        case 'category':
            $category = Category::where("id", $menuItem->reference_id)->first();
            if ($category) $url = route('category', $category->slug);
            break;

        case 'post':
            $post = Post::with(["category"])->where("id", $menuItem->reference_id)->first();
            if ($post) $url = get_permalink($post);
            break;

        default:
            $url = $menuItem->url;
            break;
    }

    return $url;
}
