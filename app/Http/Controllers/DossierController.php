<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dossier;

class DossierController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'employee') {
            // Employee: only their own dossiers
            $dossiers = $user->dossiers()->with('client')->paginate(4);
        } elseif ($user->role === 'admin') {
            // Admin: all dossiers in their organization (via user)
            $dossiers = Dossier::whereHas('user', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->with('client')
            ->paginate(4);
        } else {
            // Superadmin: all dossiers
            $dossiers = Dossier::with('client')->paginate(4);
        }

        return view('dossiers.index', ['dossiers' => $dossiers]);
    }

    public function show(Dossier $dossier)
    {
        // Eager load the documents and debts related to the dossier

        $dossier->load('documents', 'debts', 'debts.payments', 'client.financialInfo');

        return view('dossiers.show', ['dossier' => $dossier]);
    }
}
