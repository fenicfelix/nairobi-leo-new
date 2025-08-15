<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

define('status_success', '000');
define('status_error', '001');
define('status_cannot_edit', '097');
define('status_unauthorized', '098');
define('status_missing_details', '099');

class Controller extends BaseController
{
    public $data = [];
    public $theme = "";
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->data["keywords"] = get_option('ak_seo_keywords');
        $this->data["description"] = get_option('ak_seo_description');
        $this->data["logo"] = asset('theme/frontend/assets/img/logo_dark.png');
        $this->data["image"] = asset('theme/frontend/assets/img/logo_dark.png');
        $this->data["pubdate"] = date("Y-m-d H:i:sP");
        $this->data["title"] = "Home - " . get_option('ak_app_title');
        $this->theme = get_option('ak_theme');
        $this->data["global_post"] = [];
        $this->middleware(function ($request, $next) {
            $this->data["logged_user"] = Auth::user();
            return $next($request);
        });
    }
}
