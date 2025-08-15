<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PostAuthor;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
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
        $this->data["page_title"] = "Users - " . get_option('ak_app_title');
        $this->data["user_groups"] = UserGroup::all();
        $this->data["active_users"] = User::isActive()->get();
        return view('backend.pages.users.index', $this->data);
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'group_id' => 'required|exists:user_groups,id',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $group = UserGroup::where("id", "=", $request->post("group_id"))->first();
        $user_id = Auth::id();

        $user = $group->users()->create([
            'first_name' => $request->post("first_name"),
            'last_name' => $request->post("last_name"),
            "display_name" => $request->post("first_name") . " " . $request->post("last_name"),
            'phone_number' => $request->post("phone_number"),
            'email' => $request->post("email"),
            'username' => $request->post("username"),
            'password' => Hash::make($request->post("password")),
            'added_by' => $user_id,
            'updated_by' => $user_id
        ]);

        if ($user) {
            return response()->json(['status' => status_success, 'message' => "$user->first_name has been added."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "$user->first_name was not added."], Response::HTTP_OK);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_user(Request $request)
    {
        info($request->all());
        $user = User::where("id", "=", $request->post("id"))->first();
        if (!$user) return response()->json(['status' => status_error, 'message' => "User not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'group_id' => 'required|exists:user_groups,id',
            'email' =>  'required|min:3|unique:users,email,' . $user->id,
            'username' =>  'required|min:3|unique:users,username,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user->thumbnail = $request->post("thumbnail");
        $user->first_name = $request->post("first_name");
        $user->last_name = $request->post("last_name");
        $user->group_id = $request->post("group_id");
        $user->phone_number = $request->post("phone_number");
        $user->email = $request->post("email");
        $user->username = $request->post("username");

        if ($user->save()) {
            return response()->json(['status' => status_success, 'message' => "$user->first_name has been added."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "$user->first_name was not added."], Response::HTTP_OK);
        }
    }

    public function show($id)
    {
        $this->data["user"] = User::where("username", "=", $id)->first();
        return view('backend.pages.users.show', $this->data);
    }

    public function my_profile()
    {

        $this->data["user"] = Auth::user();
        return view('backend.pages.users.profile', $this->data);
    }

    public function update_profile(Request $request)
    {
        $user = User::where("id", "=", $request->post("id"))->first();
        if (!$user) return response()->json(['status' => status_error, 'message' => "User not found"], Response::HTTP_OK);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'display_name' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $user->thumbnail = $request->post("thumbnail");
        $user->first_name = $request->post("first_name");
        $user->last_name = $request->post("last_name");
        $user->phone_number = $request->post("phone_number");
        $user->display_name = $request->post("display_name");
        $user->biography = $request->post("biography");
        $user->facebook = $request->post("facebook");
        $user->twitter = $request->post("twitter");
        $user->instagram = $request->post("instagram");
        $user->linkedin = $request->post("linkedin");

        if ($user->save()) {
            return response()->json(['status' => status_success, 'message' => "$user->first_name has been saved."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "$user->first_name was not saved."], Response::HTTP_OK);
        }
    }

    public function change_password(Request $request)
    {
        $user = User::where("id", "=", $request->post("id"))->first();
        if (!$user) return response()->json(['status' => status_error, 'message' => "User not found"], Response::HTTP_OK);

        if ($request->post("task") == "change") {
            if (!Hash::check($request->post("old_password"), $user->password))
                return response()->json(['status' => status_error, 'message' => "User not found"], Response::HTTP_OK);

            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string|max:50',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
            }

            $user->password = Hash::make($request->post("new_password"));
            $user->updated_by = $user->id;

            if ($user->save()) {
                return response()->json(['status' => status_success, 'message' => "New password has been saved."], Response::HTTP_OK);
            } else {
                return response()->json(['status' => status_error, 'message' => "New password was not saved."], Response::HTTP_OK);
            }
        } else {
            $user->password = Hash::make(get_option('ak_default_password'));
            $user->updated_by = Auth::id();

            if ($user->save()) {
                return response()->json(['status' => status_success, 'message' => $user->first_name . "'s password has been reset."], Response::HTTP_OK);
            } else {
                return response()->json(['status' => status_error, 'message' => $user->first_name . "'s password was not changed."], Response::HTTP_OK);
            }
        }
    }

    public function upload_profile_image(Request $request)
    {
        info(json_encode($request->all()));
        request()->validate([
            'id' => 'required|exists:users,id',
            'file'  => 'required|mimes:jpg,jpeg,png,JPG,JPEG,PNG|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $filenamewithextension = $request->file('file')->getClientOriginalName(); //get filename with extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME); //get filename without extension
            $extension = $request->file('file')->getClientOriginalExtension(); //get file extension

            $unique = time();
            $filenametostore = $filename . '_' . $unique . '.' . $extension;
            $path = 'uploads/' . date('Y') . '/' . date('m');
            $public_path = "public/" . $path;
            $original_file_path = $path . "/" . $filenametostore;

            //Update User
            User::where("id", $request->post('id'))->update([
                "thumbnail" => $original_file_path
            ]);

            //Save original
            $request->file('file')->storeAs($public_path, $filenametostore);

            $preview_url = Storage::disk('public')->url($original_file_path);
            if ($request->post('id') == Auth::id()) {
                session(['thumbnail' => $preview_url]);
            }

            return response()->json(['status' => status_success, 'message' => "The file has been uploaded.", "path" => $original_file_path, "preview_url" => $preview_url], Response::HTTP_OK);
        }
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
            'identifier' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => status_error, 'message' => $validator->errors()->first()], Response::HTTP_OK);
        }

        $deleted = DB::transaction(function () use ($request) {
            $user = User::where("id", "=", $request->identifier)->first();
            if (PostAuthor::where("author_id", "=", $request->identifier)->exists()) {
                PostAuthor::where("author_id", $user->id)
                    ->update(
                        [
                            "author_id" => $request->user_id,
                        ]
                    );
            }

            if ($user->forceDelete()) return true;
            else return false;
        }, 2);

        if ($deleted) {
            session()->flash('success', 'The user has been deleted.');
            return response()->json(['status' => status_success, 'message' => "The user has been deleted."], Response::HTTP_OK);
        } else {
            return response()->json(['status' => status_error, 'message' => "The user was not deleted."], Response::HTTP_OK);
        }
    }
}
