<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function create()
    {
        return view('uploads.create');
    }
}
