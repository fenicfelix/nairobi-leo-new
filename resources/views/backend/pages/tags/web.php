<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\CategoriesController;
use App\Http\Controllers\Backend\DatatablesController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\PagesController;
use App\Http\Controllers\Backend\PostsController;
use App\Http\Controllers\Backend\ProgramLineupController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\ShowsController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\UserGroupsController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\Backend\VideosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Migration\WordpressController;
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
Route::get('/', [HomeController::class, 'index'])->name('/');

//Custom pages
Route::get('/about-us', [HomeController::class, 'about_us'])->name('about_us');
Route::get('/contact-us', [HomeController::class, 'contact_us'])->name('contact_us');
Route::get('/privacy-policy', [HomeController::class, 'privacy_policy'])->name('privacy_policy');
Route::get('/terms-and-conditions', [HomeController::class, 'terms_conditions'])->name('terms_conditions');


Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
//Route::get('/posts-sitemap.xml', [SitemapController::class, 'posts'])->name('posts_sitemap');
Route::get('/news-sitemap.xml', [SitemapController::class, 'posts'])->name('news_sitemap');

Route::get('/news-sitemap.xml', [SitemapController::class, 'posts'])->name('news_sitemap');
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

Route::prefix('tv')->group(function () {
    Route::get('/home', [TvController::class, 'index'])->name('tv_home');
    Route::get('/line-up/{day?}', [TvController::class, 'lineup'])->name('tv_lineup');
    Route::get('/shows/{slug?}', [TvController::class, 'shows'])->name('tv_shows');
    Route::get('/live/{id?}', [TvController::class, 'live'])->name('tv_live');
});


Route::prefix('admin')->group(function () {
    Route::middleware(['auth'])->group(
        function () {

            //Wordpress migration
            Route::get('/wp/{type}/{page?}', [WordpressController::class, 'index'])->name('wp');

            Route::post('upload-profile-image', [UsersController::class, 'upload_profile_image'])->name('upload_profile_image');
            Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

            Route::resource('tags', TagsController::class)->except(['create', 'show', 'edit', 'update']);
            Route::resource('categories', CategoriesController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);
            Route::delete('delete-category', [CategoriesController::class, 'delete_category'])->name('delete_category');
            Route::resource('posts', PostsController::class)->except(["update"]);
            Route::resource('media', MediaController::class);
            Route::resource('users', UsersController::class);
            Route::resource('pages', PagesController::class);
            Route::resource('videos', VideosController::class);
            Route::resource('shows', ShowsController::class);

            Route::post('update-user-group', [UserGroupsController::class, 'update_user_group'])->name('update_user_group');

            Route::get('/my-profile', [UsersController::class, 'my_profile'])->name('profile');

            Route::get('/filter-posts/{type}', [PostsController::class, 'index'])->name('posts.index');
            Route::post('/recover-post', [PostsController::class, 'recover_post'])->name('post.recover');
            Route::post('/posts/delete-permanently', [PostsController::class, 'delete_permanently'])->name('post.delete_permanently');

            Route::get('/fetch-tags', [TagsController::class, 'fetch_tags'])->name('fetch_tags');



            Route::post('store-options', [SettingsController::class, 'store_options'])->name('store_options');
            Route::post('update-category', [CategoriesController::class, 'update_category'])->name('update_category');
            Route::post('update-tag', [TagsController::class, 'update_tag'])->name('update_tag');
            Route::post('update-user', [UsersController::class, 'update_user'])->name('update_user');
            Route::post('update-profile', [UsersController::class, 'update_profile'])->name('update_profile');
            Route::post('change-password', [UsersController::class, 'change_password'])->name('change_password');
            Route::post('upload-file', [PostsController::class, 'upload_file'])->name('upload_file');
            Route::post('upload-intext-file', [PostsController::class, 'upload_intext_file'])->name('upload_intext_file');

            Route::post('update-image-tags', [PostsController::class, 'update_image_tags'])->name('update_image_tags');
            Route::post('update-post', [PostsController::class, 'update_post'])->name('update_post');

            Route::post('store-settings', [SettingsController::class, 'store'])->name('create_settings');
            Route::post('update-settings', [SettingsController::class, 'update'])->name('update_settings');
            Route::delete('delete-settings/{id}', [SettingsController::class, 'destroy'])->name('delete_setting');

            Route::get('media/fetch-images/{page}', [MediaController::class, 'fetch_images'])->name('media.fetch_images');

            Route::prefix('tv')->as('tv.')->group(function () {
                Route::resource('shows', ShowsController::class);
                Route::resource('program_lineup', ProgramLineupController::class)->except(["create", "show", "edit", "update"]);
            });

            Route::prefix('datatable')->as('datatable.')->group(function () {
                Route::get('/get-users', [DatatablesController::class, 'get_users'])->name('get_users');
                Route::get('/get-shows', [DatatablesController::class, 'get_shows'])->name('get_shows');
                Route::get('/get-users-groups', [DatatablesController::class, 'get_user_groups'])->name('get_user_groups');
                Route::get('/get-categories', [DatatablesController::class, 'get_categories'])->name('get_categories');
                Route::get('/get-tags', [DatatablesController::class, 'get_tags'])->name('get_tags');
                Route::get('/get-pages', [DatatablesController::class, 'get_pages'])->name('get_pages');
                Route::get('/get-videos', [DatatablesController::class, 'get_videos'])->name('get_videos');
                Route::get('/get-settings', [DatatablesController::class, 'get_settings'])->name('get_settings');
                Route::get('/get-posts/{type}', [DatatablesController::class, 'get_posts'])->name('get_posts');
            });

            Route::prefix('settings')->as('settings.')->group(function () {
                Route::get('/general', [SettingsController::class, 'general'])->name('general');
                Route::get('/advertisements', [SettingsController::class, 'advertisements'])->name('advertisements');
                Route::resource('user_groups', UserGroupsController::class)->except(['create', 'show', 'edit', 'update', 'destroy']);;
            });
        }
    );
});

Route::post('/admin/custom-login', [AdminController::class, 'login'])->name('custom_login');
// Route::get('/{slug}', [HomeController::class, 'old_post']); //Based on old WP permalink
Route::get('/{slug}/page/{keys?}', [HomeController::class, 'old_post']); //Based on old WP permalink



require __DIR__ . '/auth.php';
