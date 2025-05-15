<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function organisatie()
    {
        return view('admin.organisation');
    }

    public function medewerkers()
    {
        return view('admin.employees');
    }

    public function clienten()
    {
        return view('admin.clients');
    }
    
}
