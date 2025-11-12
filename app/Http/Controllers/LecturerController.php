<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LecturerController extends Controller
{
    /**
     * Display a list of students assigned to the logged-in supervisor.
     */
    public function index(Request $request){

        if ($request->ajax()) {
            $data = Lecturer::with(['user'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn() // adds DT_RowIndex
                ->addColumn('name', fn ($row) => $row->user->name ?? '-')
                ->addColumn('email', fn ($row) => $row->user->email ?? '-')
                ->addColumn('department', fn ($row) =>  '-')
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.lecturers');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');

        Excel::import(new LecturersImport(), $file);

        return response()->json([
            'status' => 'success',
            'message' => 'Staffs imported successfully',
        ]);
    }
    public function studentsAssigned()
{
    $supervisor = auth()->user();

    // fetch students from users table
    $students = $supervisor->assignedStudents()->get();

    return view('supervisor.students-assigned', compact('students'));
}


    /**
     * Show a single student's profile.
     */
    public function showStudent($id)
    {
        $student = Student::with('company')->findOrFail($id);

        return view('supervisor.student.show', compact('student'));
    }

    /**
     * Show reports for a specific student.
     */
    public function studentReports($id)
    {
        $student = Student::with('reports')->findOrFail($id);

        return view('supervisor.student.reports', compact('student'));
    }

    /**
     * Provide feedback for a student.
     */
    public function studentFeedback($id)
    {
        $student = Student::findOrFail($id);

        return view('supervisor.student.feedback', compact('student'));
    }

    /**
     * Show supervisor's own reports page (general, not student-specific).
     */
    public function reports()
    {
        return view('supervisor.reports');
    }

    /**
     * Show supervisor's logbook page.
     */
    public function logbook()
    {
        return view('supervisor.logbook');
    }

    /**
     * Show supervisor's evaluate page.
     */
    public function evaluate()
    {
        return view('supervisor.evaluate');
    }
}
