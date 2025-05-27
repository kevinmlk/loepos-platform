<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dossier;

class DossierController extends Controller
{
    public function index()
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get the user's dossiers
        $dossiers = $user->dossiers()->with('client')->paginate(4);

        // Return the view
        return view('dossiers.index', ['dossiers' => $dossiers]);
    }

    public function show(Dossier $dossier)
    {
        // Eager load the documents and debts related to the dossier

        $dossier->load('documents', 'debts', 'debts.payments', 'client.financialInfo');

        return view('dossiers.show', ['dossier' => $dossier]);
    }
}
