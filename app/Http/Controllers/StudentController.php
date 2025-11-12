<?php

namespace App\Http\Controllers;

use App\Models\AttachmentSchoolSupervisor;
use App\Models\AttachmentStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Company;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;



class StudentController extends Controller
{
public function index(Request $request){

    if ($request->ajax()) {
        $data = Student::with(['user'])->latest()->get();

        return DataTables::of($data)
            ->addIndexColumn() // adds DT_RowIndex
            ->addColumn('name', fn ($row) => $row->user->name ?? '-')
            ->addColumn('email', fn ($row) => $row->user->email ?? '-')
            ->addColumn('department', fn ($row) =>  '-')
            ->addColumn('program', fn ($row) =>  '-')
          //  ->addColumn('department', fn ($row) => $row->department->slug ?? '-')
           // ->addColumn('pro', fn ($row) => $row->department->slug ?? 0)

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    return view('admin.students');
}

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');

        Excel::import(new StudentsImport, $file);

        return response()->json([
            'status' => 'success',
            'message' => 'Students imported successfully',
        ]);
    }

    public function portal() {
        return view('student.portal');
    }
    public function showAttachmentForm()
    {
        $companies = Company::all();
        $logged_user = auth()->user();
        $my_student_details = Student::where('user_id', $logged_user->id)
                                        ->first();
        return view('student.attachment-form',  compact('companies',  'logged_user','my_student_details'));
    }

    public function storeAttachmentForm(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'student_phone' => 'required|string|max:20',
            'student_email' => 'required|email',

            'organization' => 'required|string|max:255',
            'date_commenced' => 'required|date',
            'date_finished' => 'required|date|after_or_equal:date_commenced',
            'town' => 'required|string|max:100',
            'street' => 'required|string|max:100',
            'building' => 'required|string|max:100',
            'supervisor_name' => 'required|string|max:255',
            'supervisor_phone' => 'required|string|max:20',
            'supervisor_email' => 'required|email',
        ]);


        return redirect()->back()->with('success', 'Attachment form submitted successfully!');
    }

    public function reports()
{
    return view('student.reports');
}
  public function logbook()
   {

    return view ('student.logbook');
   }




}

