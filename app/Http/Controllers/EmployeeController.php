<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class EmployeeController extends Controller
{
    
   public function show($id)
{
    $employee = User::findOrFail($id);

    if (request()->ajax()) {
        return view('admin.partials.employee-show', compact('employee'));
    }

    return view('admin.employees.show', compact('employee'));
}

}
