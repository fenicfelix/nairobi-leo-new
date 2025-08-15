<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\SystemPreference;
use App\Models\ProgramLineup;
use App\Models\Video;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function display_top_tv_section($onair)
{
    if ($onair) {
        if (request()->is('/') || request()->is('tv')) return true;
    }
    return false;
}

function prepare_post_body($post, $ad_one = "", $ad_two = "")
{
    $body = $post->body;
    $related_posts = fetch_related_posts($post, 3);

    $ad_code_one = "";
    $ad_code_two = "";

    if (get_option($ad_one)) {
        $ad_code_one = '
            <div class="col-12 col-md-6 offset-md-3 text-center ad py-4 mb-4">
                ' . get_option($ad_one) . '
            </div>';
    }

    if (get_option($ad_two)) {
        $ad_code_two = '
            <div class="col-12 col-md-6 offset-md-3 text-center ad py-4 mb-4">
                ' . get_option($ad_two) . '
            </div>';
    }


    $closing_p = '</p>';
    $paragraphs = explode($closing_p, $body);

    foreach ($paragraphs as $index => $paragraph) {

        if (trim($paragraph)) {
            $paragraphs[$index] .= $closing_p;
        }

        if ($index == 2) {
            $paragraphs[$index] .= $ad_code_one;
        }

        if ($index == 6) {
            $paragraphs[$index] .= $ad_code_two;
        }

        if ($index == 4) {
            if (sizeof($related_posts) > 0) {
                $also_read = view('theme.' . get_option('ak_theme') . '.templates.also-read', ['related_posts' => $related_posts]);
                $paragraphs[$index] .= $also_read;
            }
        }
    }


    return implode('', $paragraphs);;
}

function social_icons()
{
    return load_theme('templates.social-icons');
}

function get_onair_show()
{
    $onair = ProgramLineup::with(["show.banner", "show.mobile_banner"])->isOnAir()->first();
    return ($onair && $onair->show && $onair->show->banner) ? $onair : false;
}

function get_show_hosts($show)
{
    return $show->hosts;
}

function fetch_youtube_videos($limit, $size)
{
    $videos = Video::isPublished()->orderBy('published_at', 'DESC')->skip(0)->take(10)->get();
    return view('theme.tv47.templates.youtube-videos', ['videos' => $videos, 'size' => 'sm']);
}
