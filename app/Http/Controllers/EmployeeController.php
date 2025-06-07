<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmployeeController extends Controller
{
    public function create()
    {
        return view('admin.employees.create');
    }

    public function show($id)
    {
        $employee = User::findOrFail($id);

        if (request()->ajax()) {
            return view('admin.partials.employee-show', compact('employee'));
        }

        return view('admin.employees.show', compact('employee'));
    }

    public function store()
    {
        $organizationId = auth()->user()->organization_id;

        // Validate the request
        $attributes = request()->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // Do not validate role and organization_id here
        ]);

        // Set the role and organization_id explicitly
        $attributes['role'] = User::ROLE_EMPLOYEE;
        $attributes['organization_id'] = $organizationId;

        // Create the employee
        $employee = User::create($attributes);

        // Redirect to the employee's profile
        return redirect()
            ->route('admin.employees.show', $employee->id)
            ->with('success', 'Employee created successfully.');
    }
}
