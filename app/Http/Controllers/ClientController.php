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

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $attributes = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'national_registry_number' => ['nullable', 'string', 'max:255'],
        ]);

        // Create the client
        $client = Client::create($attributes);

        Dossier::create([
            'client_id' => $client->id,
            'user_id' => 1,
            'status' => Dossier::STATUS_IN_PROCESS,
            'type' => Dossier::TYPE_BUDGET_MANAGEMENT,
        ]);

        // Redirect to the client's profile or list
        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', ['client' => $client]);
    }

    public function update(Client $client)
    {
        // Validate the request
        $attributes = request()->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients,email,' . $client->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            'national_registry_number' => ['nullable', 'string', 'max:255'],
        ]);

        // Update the client
        $client->update($attributes);
        // Update the associated dossier's updated_at column
        if ($client->dossiers()->exists()) {
            $client->dossiers()->update(['updated_at' => now()]);
        }

        // Redirect to the client's profile or list
        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client updated successfully.');
    }

}
