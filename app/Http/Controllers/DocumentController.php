<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
// Import Document model
use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        return view("documents.index");
    }

    public function create()
    {
        return view("documents.create");
    }

    public function store(Request $request) {}
}
