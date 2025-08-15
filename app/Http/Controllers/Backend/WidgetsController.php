<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WidgetsController extends Controller
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
        $this->data["page_title"] = "Widgets - " . get_option('ak_app_title');
        $this->data["widget"] = [];
        return view('backend.pages.widgets.index', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_update_widget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|min:3',
            'slug' =>  'required|min:3|unique:widgets,slug,' . $request->post('id'),
            'body' =>  'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        if ($request->post("id")) {
            $widget = Widget::where("id", $request->post("id"))->first();
            if (!$widget) return response()->json(['status' => status_error, 'message' => "The widget was not found. Please try again."], Response::HTTP_OK);
            $widget->title = $request->post("title");
            $widget->slug = $request->post("slug");
            $widget->body = $request->post("body");

            if ($widget->save()) {
                return response()->json(['status' => status_success, 'message' => "The widget has been updated successfully."], Response::HTTP_OK);
            } else {
                return response()->json(['status' => status_error, 'message' => "The widget could not be updated. Please try again."], Response::HTTP_OK);
            }
        } else {
            $widget = Widget::query()->create([
                "title" => $request->post("title"),
                "slug" => $request->post("slug"),
                "body" => $request->post("body"),
                "last_updated_at" => date("Y-m-d H:i:s"),
                "last_updated_by" => Auth::id(),
            ]);

            if ($widget) {
                return response()->json(['status' => status_success, 'id' => $widget->id, 'message' => "The widget has been added successfully."], Response::HTTP_OK);
            } else {
                return response()->json(['status' => status_error, 'message' => "The widget could not be added. Please try again."], Response::HTTP_OK);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data["page_title"] = "Widgets - " . get_option('ak_app_title');
        $this->data["widget"] = Widget::where("id", $id)->first();
        return view('backend.pages.widgets.index', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $widget = Widget::where("id", "=", $id)->first();
        if (!$widget) {
            return response()->json(['status' => status_error, 'message' => "The widget does not exist."], Response::HTTP_OK);
        }

        if (!$widget->forceDelete()) return response()->json(['status' => status_error, 'message' => "The widget was not deleted."], Response::HTTP_OK);
        return response()->json(['status' => status_success, 'message' => "The widget has been permanently deleted."], Response::HTTP_OK);
    }
}
