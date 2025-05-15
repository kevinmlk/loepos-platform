<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    // Show the login view
    public function create()
    {
        return view("auth.login");
    }

    // Function to validate and login the user
    public function store()
    {
        // Validate the form data
        $attributes = request()->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);

        // Attempt to login the user
        if (!Auth::attempt($attributes)) {
            throw ValidationException::withMessages([
                "email" => "These credentials do not match our records.",
                "password" => "Password is incorrect.",
            ]);
        }

        // Regenerate the session token
        request()->session()->regenerate();

        // Redirect to the dashboard
        return redirect("/");
    }

    // Function to logout the user
    public function destroy()
    {
            Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login')->with('status', 'U bent succesvol uitgelogd.');
    }
}
