<?php

namespace App\Http\Controllers;

use App\Imports\LecturersImport;
use Illuminate\Support\Facades\Auth;
use App\Models\WeeklyReport;
use App\Models\FinalReport;
use App\Models\AttachmentLecturer;
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
        $data = Student::with('user', 'program', 'program.parent')
            ->whereHas('user')
            ->orderBy(User::select('name')->whereColumn('users.id', 'students.user_id'))
            ->get();

        return DataTables::of($data)
            ->addIndexColumn() // adds DT_RowIndex
            ->addColumn('name', fn ($row) => $row->user->name ?? '-')
            ->addColumn('email', fn ($row) => $row->user->email ?? '-')
            ->addColumn('department', fn ($row) => $row->program->parent->name ??  '-')
            ->addColumn('program', fn ($row) => $row->program->name ?? '-')
           // ->addColumn('pro', fn ($row) => $row->department->slug ?? 0)

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    return view( 'admin.students');
}

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'status'        => 'success',
                'message'        => 'Upload completed',
                'success_count'  => $import->successCount,
                'fail_count'     => count($import->failedRecords),
                'failed_records' => $import->failedRecords
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function portal() {
        return view('student.portal');
    }
  public function storeWeeklyReport(Request $request)
{
    $request->validate([
        'week_id' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
        'week_start_date' => 'required|date',
        'week_end_date' => 'required|date',
        'weekly_report' => 'required|string',
    ]);

    $student = auth()->user()->student;
    if (!$student) {
        return back()->withErrors('Student record not found.');
    }

    $attachmentId = session('attachment_id');

    $attachmentstudent = AttachmentStudent::where('student_id', $student->id)
        ->where('attachment_id', $attachmentId)
        ->first();

    if (!$attachmentstudent) {
        return back()->withErrors('You are not registered for this attachment.');
    }

    // --- ADD THIS CHECK HERE ---
    $exists = WeeklyReport::where('attachment_student_id', $attachmentstudent->id)
        ->where('week_id', $request->week_id)
        ->exists();

    if ($exists) {
        return back()->withInput()->withErrors("A weekly report for Week {$request->week_id} has already been submitted.");
    }
    // ----------------------------

    WeeklyReport::create([
        'attachment_student_id' => $attachmentstudent->id,
        'week_id' => $request->week_id,
        'week_start_date' => $request->week_start_date,
        'week_end_date' => $request->week_end_date,
        'weekly_report' => $request->weekly_report,
        'is_approved' => false,
    ]);

    return back()->with('success', 'Weekly report submitted successfully.');
}
public function weeklyReports()
{
    $user = auth()->user();
    $user_role = 'guest';

    if ($user->isStudent()) {
        $user_role = 'student';
        $student = $user->student;

        // 1. Get the specific enrollment record for this student and current period
        $enrollment = \App\Models\AttachmentStudent::where('student_id', $student->id)
            ->where('attachment_id', session('attachment_id'))
            ->first();

        // 2. Fetch reports using the ENROLLMENT ID (attachment_student_id)
        $weeklyReports = $enrollment 
            ? WeeklyReport::where('attachment_student_id', $enrollment->id)
                ->orderBy('week_id', 'asc')
                ->get()
            : collect();

    } elseif ($user->role === 'industrial_supervisor') { // Matches your 'isIndustry' check
        $user_role = 'industrial_supervisor';
        
        // 1. Get the Supervisor's profile ID
        $supervisor = \App\Models\IndustrialSupervisor::where('user_id', $user->id)->first();

        if (!$supervisor) {
            $weeklyReports = collect();
        } else {
            // 2. Fetch reports for all students assigned to this supervisor
            $weeklyReports = WeeklyReport::whereIn('attachment_student_id', function ($query) use ($supervisor) {
                $query->select('id')
                      ->from('attachment_students')
                      ->where('industrial_supervisor_id', $supervisor->id);
            })
            ->with(['attachmentStudent.student.user']) // Ensure these relationships exist in your models
            ->orderBy('created_at', 'desc')
            ->get();
        }

    } elseif ($user->role === 'lecturer') {
        $user_role = 'lecturer';
        $lecturer = \App\Models\Lecturer::where('user_id', $user->id)->first();

        // Fetch reports for students assigned to this lecturer
        $weeklyReports = WeeklyReport::whereIn('attachment_student_id', function ($query) use ($lecturer) {
            $query->select('id')
                  ->from('attachment_students')
                  ->where('lecturer_id', $lecturer->id);
        })
        ->with(['attachmentStudent.student.user'])
        ->orderBy('created_at', 'desc')
        ->get();

    } else {
        $weeklyReports = collect();
    }

    return view('student.weekly-reports', compact('weeklyReports', 'user_role'));
}
public function storeFinalReport(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'final_report_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
    ]);

    $attachmentStudent = Auth::user()->student->attachmentStudent;

    if (!$attachmentStudent) {
        return back()->withErrors('Attachment record not found.');
    }

    // STRICT "SUBMIT ONCE" CHECK
    if (FinalReport::where('attachment_student_id', $attachmentStudent->id)->exists()) {
        return back()->withErrors('You have already submitted your final report.');
    }

  // Old: $path = $request->file('final_report_file')->store('reports/final');
$path = $request->file('final_report_file')->store('reports/final', 'public');

    FinalReport::create([
        'attachment_student_id' => $attachmentStudent->id,
        'title' => $request->title,
        'content' => $request->content,
        'file_path' => $path,
        'is_submitted' => true,
    ]);

    return redirect()->back()->with('success', 'Final report submitted successfully.');
}
public function finalReport()
{
    $student = Auth::user()->student;
    $attachmentStudent = $student->attachmentStudent;

   
    $final_report = FinalReport::where('attachment_student_id', $attachmentStudent->id)->first();

    
    return view('student.final-report', compact('final_report', 'attachmentStudent'));
}
}