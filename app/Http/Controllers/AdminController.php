<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function organisatie()
    {
        return view('admin.organisation');
    }

    public function medewerkers()
    {
        $users = \App\Models\User::all(); // Or filter by role if needed
        
        return view('admin.employees', compact('users'));
    }

    public function clienten()
    {
        return view('admin.clients');
    }
    
    
}
