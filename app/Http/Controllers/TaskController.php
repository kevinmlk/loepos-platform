<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dossiers = $user->dossiers;
        $documents = $dossiers->flatMap(function ($dossier) {
            return $dossier->documents;
        });
        $tasks = $documents->flatMap(function ($document) {
            return $document->tasks;
        });
        // $tasks = $user->dossiers()->documents()->tasks();

        return view('tasks.index', ['tasks' => $tasks]);
    }
}
