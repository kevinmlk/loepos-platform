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
}
