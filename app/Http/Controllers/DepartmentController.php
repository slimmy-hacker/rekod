<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\School;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('school')->get();
        return view('admin.departments', compact('departments'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.departments.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'school_code' => 'required|exists:schools,code',
        ]);

        Department::create([
            'name' => $request->name,
            'school_code' => $request->school_code,
        ]);

        return redirect()->route('admin.departments')
                         ->with('success', 'Department uploaded successfully');
    }
}
