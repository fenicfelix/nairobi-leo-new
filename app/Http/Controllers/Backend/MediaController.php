<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
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
        $this->data["page_title"] = "Media - " . get_option('ak_app_title');
        return view('backend.pages.media.index', $this->data);
    }

    public function get_images(Request $request)
    {
        info(json_encode($request->all()));
    }

    public function fetch_images(Request $request)
    {
        $limit = 20;
        $current_page = $request->get('page');
        $offset = (($limit * $current_page) - $limit);

        $query = DB::table('images')->orderBy('id', 'DESC');
        if ($request->get("s")) {
            $query->where('alt_text', 'like', '%' . $request->get('s') . '%')
                ->orWhere('title', 'like', '%' . $request->get('s') . '%')
                ->orWhere('caption', 'like', '%' . $request->get('s') . '%')
                ->orWhere('description', 'like', '%' . $request->get('s') . '%');
        }
        $total_items = $query->count();
        $pages = floor($total_items / $limit);

        $previous_page = 0;
        $next_page = 0;

        if ($current_page > 1) {
            $previous_page = $current_page;
            $previous_page--;
        }
        if ($total_items > (($limit * $offset) + $limit)) {
            $next_page = $current_page;
            $next_page++;
        }

        $html = "";
        $items = $query->skip($offset)->take($limit)->get();
        if ($items) {
            foreach ($items as $item) {
                $thumbnail = Storage::disk('public')->url($item->file_name);
                $html .= '<div class="col">
                                <input type="radio" id="media-' . $item->id . '" name="media" class="form-check-input media-select">
                                <div class="card">
                                    <img data-id="' . $item->id . '" src="' . $thumbnail . '" alt="' . $item->alt_text . '" class="card-img media-select" loading="lazy" data-alt="' . $item->alt_text . '" data-title="' . $item->title . '" data-caption="' . $item->caption . '" data-description="' . $item->description . '">
                                </div>
                            </div>';
            }
        }

        $pagination = '';

        $result = [
            "status" => status_success,
            "items" => $html,
            "previous_page" => $previous_page,
            "next_page" => $next_page
        ];

        return response()->json($result, Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        info(json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'identifier' =>  'required|exists:images,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($request) {
            $image = Image::where("id", "=", $request->post('identifier'))->first();
            if (!$image) return false;

            $thumbnails = get_thumbnail_sizes();
            if ($thumbnails) {
                foreach ($thumbnails as $key => $value) {
                    if (!delete_image($image->file_name, $key)) return false;
                }
            }

            if (!delete_image($image->file_name, "original")) return false;

            if ($image->forceDelete()) return true;
            else return false;
        }, 2);

        if ($deleted) {
            session()->flash("success", "The image has been deleted");
            return response()->json(['status' => status_success, 'message' => "The image has been deleted."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The image was not deleted."], Response::HTTP_OK);
        }
    }
}
