<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Organization;

class UserController extends Controller
{
    public function index()
    {
        // Check if the user is an admin or superadmin
        if (!auth()->user()->role === User::ROLE_SUPERADMIN) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all users
        $users = User::paginate(6);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $organizations = Organization::all();
        $roles = User::ROLES;

        return view('users.create', [
            'organizations' => $organizations,
            'roles' => $roles,
        ]);
    }

    public function show(User $user)
    {
        $organizations = Organization::all();
        $roles = User::ROLES;
        return view('users.show', [
            'user' => $user,
            'organizations' => $organizations,
            'roles' => $roles,
        ]);
    }

    public function store()
    {
        // Validate the request
        $attributes = request()->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'organization' => ['required', 'exists:organizations,id'],
            'role_select' => ['required', 'in:' . implode(',', User::ROLES)],
        ]);

        // Set the role and organization_id from the form
        $attributes['role'] = $attributes['role_select'];
        $attributes['organization_id'] = $attributes['organization'];
        unset($attributes['role_select'], $attributes['organization']);

        // Create the employee
        $employee = User::create($attributes);

        // Redirect to the employee's profile
        return redirect()
            ->route('users.show', $employee->id)
            ->with('success', 'User created successfully.');
    }

    public function update(User $user)
    {
        // Validate only the fields present in the request
        $attributes = request()->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'organization' => ['sometimes', 'exists:organizations,id'],
            'role_select' => ['sometimes', 'in:' . implode(',', User::ROLES)],
        ]);

        // If organization is present, set organization_id
        if (array_key_exists('organization', $attributes)) {
            $attributes['organization_id'] = $attributes['organization'];
            unset($attributes['organization']);
        }

        // If role_select is present, set role
        if (array_key_exists('role_select', $attributes)) {
            $attributes['role'] = $attributes['role_select'];
            unset($attributes['role_select']);
        }

        // If password is not present, don't update it
        if (empty($attributes['password'])) {
            unset($attributes['password']);
        }

        $user->update($attributes);

        return redirect()
            ->route('users.show', $user->id)
            ->with('success', 'User updated successfully.');
    }
}
