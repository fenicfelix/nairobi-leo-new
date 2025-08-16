<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\CategoriesController;
use App\Http\Controllers\Backend\DatatablesController;
use App\Http\Controllers\Backend\Ecommerce\OrdersController;
use App\Http\Controllers\Backend\Ecommerce\ProductsController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\MenuItemController;
use App\Http\Controllers\Backend\PagesController;
use App\Http\Controllers\Backend\PostsController;
use App\Http\Controllers\Backend\ProgramLineupController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\ShowsController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\UserGroupsController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\Backend\VideosController;
use App\Http\Controllers\Backend\WidgetsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Migration\CIController;
use App\Http\Controllers\Migration\WordpressController;
use App\Http\Controllers\RSSController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TvController;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::any('/{category}/{id}/{slug}', HomeController::class, 'single');

Route::get('/admin', function () {
    return redirect()->route('login');
})->middleware('guest');

Route::get('/admin', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('more-stories/{type}/{value}', [HomeController::class, 'more_stories'])->name('load_more');

//Custom pages
Route::get('/{slug}.html', [HomeController::class, 'page'])->name('page');

Route::get('/feed', [RSSController::class, 'index'])->name('rss');
Route::get('/feeds', [RSSController::class, 'feeds'])->name('feeds');
Route::get('/feed/{category}', [RSSController::class, 'feed'])->name('category_rss');


Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/google-news-sitemap.xml', [SitemapController::class, 'googleNewsSitemap'])->name('google_news_sitemap.index');
Route::get('/news-sitemap.xml', [SitemapController::class, 'posts'])->name('posts_sitemap.index');
Route::get('/posts-sitemap-{page?}.xml', [SitemapController::class, 'posts'])->name('posts_sitemap');
Route::get('/categories-sitemap.xml', [SitemapController::class, 'categories'])->name('categories_sitemap');
Route::get('/tags-sitemap.xml', [SitemapController::class, 'tags'])->name('tags_sitemap');
Route::get('/authors-sitemap.xml', [SitemapController::class, 'authors'])->name('authors_sitemap');
Route::get('/pages-sitemap.xml', [SitemapController::class, 'pages'])->name('pages_sitemap');

Route::get('search/', [HomeController::class, 'search'])->name('search');
Route::get('topic/{topic}', [HomeController::class, 'tags'])->name('topics');
Route::get('preview/{id}', [HomeController::class, 'preview'])->name('preview');
Route::get('author/{author}', [HomeController::class, 'author'])->name('author');
Route::get('/{category}/article/{id}/{slug}', [HomeController::class, 'post'])->name('post');
Route::get('/category/{category}', [HomeController::class, 'category'])->name('category');
Route::get('{slug}/', [HomeController::class, 'old_post']);

Route::post('api/hide-breaking-news', [PostsController::class, 'hide_breaking_news'])->name('hide_breaking_news');


Route::prefix('tv')->group(function () {
    Route::get('/home', [TvController::class, 'index'])->name('tv_home');
    Route::get('/line-up/{day?}', [TvController::class, 'lineup'])->name('tv_lineup');
    Route::get('/shows/{slug?}', [TvController::class, 'shows'])->name('tv_shows');
    Route::get('/live/{id?}', [TvController::class, 'live'])->name('tv_live');
});

Route::prefix('ecommerce')->middleware(['auth'])->group(function () {

    Route::resource('products', ProductsController::class)->except(["update", "store"]);
    Route::post('products/update-product', [ProductsController::class, 'create_update_post'])->name('update_product');

    Route::get('/orders', [OrdersController::class, 'index'])->name('ecommerce.orders');
});


Route::prefix('admin')->group(function () {
    Route::middleware(['auth'])->group(
        function () {
            //Wordpress migration
            Route::get('/wp/{type}/{page?}', [WordpressController::class, 'index'])->name('wp');
            Route::get('/ci/{type}/{page?}', [CIController::class, 'index'])->name('ci');

            Route::post('upload-profile-image', [UsersController::class, 'upload_profile_image'])->name('upload_profile_image');
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');



            Route::resource('tags', TagsController::class)->except(['create', 'show', 'edit', 'update']);
            Route::resource('categories', CategoriesController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
            Route::delete('delete-category', [CategoriesController::class, 'delete_category'])->name('delete_category');
            Route::resource('posts', PostsController::class)->except(["update", "store"]);
            Route::resource('media', MediaController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
            Route::post('delete-image', [MediaController::class, 'destroy'])->name('delete_image');
            Route::resource('users', UsersController::class)->except(["destroy"]);
            Route::resource('pages', PagesController::class);
            Route::resource('videos', VideosController::class)->except(['create', 'show', 'edit', 'update']);
            Route::resource('shows', ShowsController::class);

            Route::post('update-user-group', [UserGroupsController::class, 'update_user_group'])->name('update_user_group');

            Route::get('/my-profile', [UsersController::class, 'my_profile'])->name('profile');
            Route::get('/get-menu-items', [MenuController::class, 'get_menu_items'])->name('get_menu_items');

            Route::get('/filter-posts/{type}', [PostsController::class, 'index'])->name('posts.index');
            Route::post('/recover-post', [PostsController::class, 'recover_post'])->name('post.recover');
            Route::post('/posts/delete-permanently', [PostsController::class, 'delete_permanently'])->name('post.delete_permanently');
            Route::post('/take-over-post', [PostsController::class, 'take_over_post'])->name('take_over_post');

            Route::get('/fetch-tags', [TagsController::class, 'fetch_tags'])->name('fetch_tags');



            Route::post('store-options', [SettingsController::class, 'store_options'])->name('store_options');
            Route::post('update-category', [CategoriesController::class, 'update_category'])->name('update_category');
            Route::post('update-tag', [TagsController::class, 'update_tag'])->name('update_tag');
            Route::post('update-user', [UsersController::class, 'update_user'])->name('update_user');
            Route::post('delete-user', [UsersController::class, 'destroy'])->name('delete_user');
            Route::post('update-profile', [UsersController::class, 'update_profile'])->name('update_profile');
            Route::post('change-password', [UsersController::class, 'change_password'])->name('change_password');
            Route::post('upload-file', [PostsController::class, 'upload_file'])->name('upload_file');
            Route::post('upload-intext-file', [PostsController::class, 'upload_intext_file'])->name('upload_intext_file');

            Route::post('update-image-tags', [PostsController::class, 'update_image_tags'])->name('update_image_tags');
            Route::post('posts/update-post', [PostsController::class, 'create_update_post'])->name('update_post');
            Route::post('widgets/update-widget', [WidgetsController::class, 'create_update_widget'])->name('update_widget');

            Route::post('store-settings', [SettingsController::class, 'store'])->name('create_settings');
            Route::post('update-settings', [SettingsController::class, 'update'])->name('update_settings');
            Route::delete('delete-settings/{id}', [SettingsController::class, 'destroy'])->name('delete_setting');

            Route::get('media/images/fetch', [MediaController::class, 'fetch_images'])->name('media.fetch_images');

            Route::prefix('tv')->as('tv.')->group(function () {
                Route::resource('shows', ShowsController::class);
                Route::resource('program_lineup', ProgramLineupController::class)->except(["create", "show", "edit", "update"]);
            });

            Route::prefix('datatable')->as('datatable.')->group(function () {
                Route::get('/get-users', [DatatablesController::class, 'get_users'])->name('get_users');
                Route::get('/get-shows', [DatatablesController::class, 'get_shows'])->name('get_shows');
                Route::get('/get-users-groups', [DatatablesController::class, 'get_user_groups'])->name('get_user_groups');
                Route::get('/get-categories', [DatatablesController::class, 'get_categories'])->name('get_categories');
                Route::get('/get-products', [DatatablesController::class, 'get_products'])->name('get_products');
                Route::get('/get-tags', [DatatablesController::class, 'get_tags'])->name('get_tags');
                Route::get('/get-pages', [DatatablesController::class, 'get_pages'])->name('get_pages');
                Route::get('/get-videos', [DatatablesController::class, 'get_videos'])->name('get_videos');
                Route::get('/get-settings', [DatatablesController::class, 'get_settings'])->name('get_settings');
                Route::get('/get-widgets', [DatatablesController::class, 'get_widgets'])->name('get_widgets');
                Route::get('/get-posts/{type}', [DatatablesController::class, 'get_posts'])->name('get_posts');
            });

            Route::prefix('settings')->as('settings.')->group(function () {
                Route::resource('/widgets', WidgetsController::class)->except(["create", "show", "update"]);
                Route::get('/general', [SettingsController::class, 'general'])->name('general');
                Route::get('/advertisements', [SettingsController::class, 'advertisements'])->name('advertisements');
                Route::resource('user_groups', UserGroupsController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
                Route::resource('menus', MenuController::class)->except(['create', 'show', 'edit']);
                Route::resource('menu_items', MenuItemController::class)->except(['index', 'create', 'show', 'edit', 'destroy']);
            });
        }
    );
});

Route::post('/admin/custom-login', [AdminController::class, 'login'])->name('custom_login');
// Route::get('/{slug}', [HomeController::class, 'old_post']); //Based on old WP permalink
Route::get('/{slug}/page/{keys?}', [HomeController::class, 'old_post']); //Based on old WP permalink
Route::get('/{category}/{postId}/{slug}', [HomeController::class, 'old_ci_post']); //Based on old CI permalink
Route::get('/sitemap/news', [SitemapController::class, 'news']);
Route::get('/test-news', function () {
    $page = 1;
    $limit = 100;
    $news = App\Models\Post::isPublished()
        ->where('published_at', '>=', Carbon\Carbon::now()->subDays(2))
        ->with(["main_image", "category"])
        ->orderBy('published_at', 'DESC')
        ->skip(($page - 1) * $limit)
        ->take($limit)
        ->get();

    return $news->toJson();
});


require __DIR__ . '/auth.php';
