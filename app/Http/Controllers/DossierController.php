<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DossierController extends Controller
{
    public function index()
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get the user's dossiers
        $dossiers = $user->dossiers()->with('client')->paginate(4);

        // Return the view
        return view('dossiers.index', compact('dossiers'));
    }
}
