<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{

    public function adddepartment(){

        $departments = Department::all();
        return view('admin.adddepartment', compact('departments'));
    }


    public function store(Request $request)
    {
         Department::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department deleted successfully.');
    }

    public function edit(Department $department)
    {
        return view('admin.edit.edit-department', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $department->update($validatedData);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

}
