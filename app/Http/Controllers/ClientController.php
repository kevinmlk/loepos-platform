<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Dossier;

class ClientController extends Controller
{
        public function index()
    {
        $clients = Client::with('dossiers')->get();
        return view('clients', compact('clients'));
    }

}
