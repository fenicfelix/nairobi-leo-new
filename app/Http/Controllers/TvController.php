<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;
use App\Models\ProgramLineup;
use App\Models\Video;

class TvController extends Controller
{
    public function index()
    {
        $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
        $description = $this->data["description"];
        $this->data["description"] = "";
        $this->data['onair'] = get_onair_show() ?? false;
        $this->data["shows"] = Show::select("id", "title", "slug", "banner_img", "hosts")->with(["banner"])->where("active", "=", "1")->orderByRaw('RAND()')->get();
        $this->data["lineup"] = ProgramLineup::with(["show.banner"])->where("day", "=", $this->resolve_day(strtolower(date("l"))))->orderBy("start_time", "ASC")->get();
        return load_theme('tv.index', $this->data);
    }

    public function lineup($day = NULL)
    {
        $day = $day ?? strtolower(date("l"));
        $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
        $description = $this->data["description"];
        $this->data["description"] = "";
        $this->data["lineup"] = ProgramLineup::with(["show.banner"])->where("day", "=", $this->resolve_day($day))->orderBy("start_time", "ASC")->get();
        $this->data["day"] = $day;
        return load_theme('tv.lineup', $this->data);
    }

    private function resolve_day($day)
    {
        return date("w", strtotime($day));
    }

    public function shows($slug = NULL)
    {
        if ($slug) {
            $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
            $description = $this->data["description"];
            $this->data["description"] = "";
            $this->data["show"] = Show::with(["banner"])->where("slug", "=", $slug)->first();
            return load_theme('tv.show', $this->data);
        } else {
            $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
            $description = $this->data["description"];
            $this->data["description"] = "";
            $this->data["shows"] = Show::select("id", "title", "slug", "banner_img", "hosts")->with(["banner"])->where("active", "=", "1")->get();
            return load_theme('tv.shows', $this->data);
        }
    }

    public function live($id = NULL)
    {
        if ($id) {
            $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
            $this->data["video"] = Video::where("video_id", "=", $id)->first();
            return load_theme('tv.video', $this->data);
        } else {
            $this->data["videos"] = Video::isPublished()->orderBy('published_at', 'DESC')->skip(0)->take(8)->get();
            $this->data["title"] = "Watch TV - " . get_option('ak_app_title');
            $this->data["video_id"] = $id;
            return load_theme('tv.live', $this->data);
        }
    }
}
