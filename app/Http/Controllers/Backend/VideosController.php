<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->group_id > 2) return redirect('/')->with('warning', 'You are not allowed to access the page.');
        unset_current_editor();
        $this->data["page_title"] = "Videos - " . get_option('ak_app_title');
        return view('backend.pages.videos.index', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $video = Video::where("id", "=", $id)->first();
        if (!$video) {
            return response()->json(['status' => status_error, 'message' => "The video does not exist."], Response::HTTP_OK);
        }

        $video->published = "0";
        $video->updated_by = Auth::id();
        $video->updated_at = date('Y-m-d H:i:s');
        if (!$video->save()) return response()->json(['status' => status_error, 'message' => "The video was not trashed."], Response::HTTP_OK);

        return response()->json(['status' => status_success, 'message' => "The post has been trashed."], Response::HTTP_OK);
    }
}
