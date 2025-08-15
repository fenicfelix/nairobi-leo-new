<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserGroupsController extends Controller
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
        $this->data["page_title"] = "User groups - " . get_option('ak_app_title');
        return view('backend.pages.settings.groups', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|unique:user_groups',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user_group = UserGroup::query()->create([
            "name" => $request->post("name"),
            "description" => $request->post("description"),
        ]);

        if ($user_group) {
            return response()->json(['status' => status_success, 'message' => "The user group has been added successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The user group could not be added. Please try again."], Response::HTTP_OK);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_user_group(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required:exists,user_groups',
            'name' => 'required|string|min:3|unique:user_groups,name,' . $request->post('id'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user_group = UserGroup::where("id", "=", $request->post('id'))->first();
        $user_group->name = $request->post('name');
        $user_group->description = $request->post('description');

        if ($user_group->save()) {
            return response()->json(['status' => status_success, 'message' => "The user group has been saved successfully."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The user group could not be saved. Please try again."], Response::HTTP_OK);
        }
    }
}
