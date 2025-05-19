<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $documents = Document::paginate(5);

        return view('dashboard.index', [
            'documents' => $documents,
        ]);
    }
}
