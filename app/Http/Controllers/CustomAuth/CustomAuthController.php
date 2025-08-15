<?php

namespace App\Http\Controllers\CustomAuth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomAuthController extends Controller
{
    public function create() {
        //Add the previous url to session
        session()->put('next_url', url()->previous());
        return view('auth.register');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $name = explode(" ", $request->name);
        $other_names = str_replace($name[0]." ", "", $request->name);

        $user = User::create([
            'first_name' => $name["0"],
            'last_name' => $other_names,
            'display_name' => $request->name,
            'username' => $request->email,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => 5
        ]);

        event(new Registered($user));

        Auth::login($user);

        if(session()->has("next_url")) {
            $url = session()->pull("next_url");
            return redirect($url);
        } else return redirect()->route('/');
    }
}
