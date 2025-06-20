<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationController extends Controller
{
    // Display all organizations with pagination
    public function index()
    {
        // Fetch paginated organizations, 10 per page (you can change the number)
        $organizations = Organization::paginate(9);

        return view('organisations.organisations', compact('organizations'));
    }

    public function create()
    {
        return view('organisations.create');
    }

    public function show(Organization $organization)
    {
        return view('organisations.show', compact('organization'));
    }

        public function edit(Organization $organization)
    {
        return view('organisations.edit', compact('organization'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'VAT' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $organization = Organization::create($validated);

        return redirect()->route('organisations.show', $organization)->with('success', 'Organisatie succesvol aangemaakt.');
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $organization->update($validated);

        return redirect()->route('organisations.show', $organization)->with('success', 'Organisatie succesvol bijgewerkt.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('organisations.index')->with('success', 'Organisatie succesvol verwijderd.');
    }
}
