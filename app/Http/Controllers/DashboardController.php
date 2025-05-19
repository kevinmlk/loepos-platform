<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->take(4)->get();

        return view('dashboard.index', [
            'documents' => $documents,
        ]);
    }

    /**
    * TODO: Get daily uploaded documents counts from monday to friday
    *
    * @return array
    */
    public function getDailyUploadedDocuments(): array
    {

    }
}
