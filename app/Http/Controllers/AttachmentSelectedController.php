<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\AttachmentLecturer;
use App\Models\AttachmentStudent;
use App\Models\Company;
use App\Models\IndustrialSupervisor;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentSelectedController extends Controller
{public function index()
{
    $role = Auth::user()->role;

    if ($role === 'student') {
        $student = Student::where('user_id', Auth::id())->first();
        
        
        $enrollment = AttachmentStudent::where('student_id', $student->id)->first();

        if (!$enrollment) {
            
            $attachments = Attachment::orderBy('start_date', 'desc')->get();
            return view('attachment_selected.index', compact('attachments'));
        }

        $attachment_students = AttachmentStudent::with('attachment')
            ->where('student_id', $student->id)
            ->get();
        return view('attachment_selected.students', compact('attachment_students'));
    }

    if ($role === 'lecturer') {
        $lecturer = Lecturer::where('user_id', Auth::id())->first();
        $hasPeriod = AttachmentLecturer::where('lecturer_id', $lecturer->id)->exists();

        if (!$hasPeriod) {
            $attachments = Attachment::orderBy('start_date', 'desc')->get();
            return view('attachment_selected.index', compact('attachments'));
        }

        $attachment_lecturers = AttachmentLecturer::with('attachment')
            ->where('lecturer_id', $lecturer->id)
            ->get();
        return view('attachment_selected.lecturers', compact('attachment_lecturers'));
    }

    if ($role === 'industrial_supervisor') {
        $supervisor = IndustrialSupervisor::where('user_id', Auth::id())->first();

        
        $assigned_ids = AttachmentStudent::where('industrial_supervisor_id', $supervisor->id)
            ->distinct()
            ->pluck('attachment_id');

        
        if ($assigned_ids->isEmpty()) {
            $attachments = Attachment::orderBy('start_date', 'desc')->get();
        } else {
            $attachments = Attachment::whereIn('id', $assigned_ids)
                ->orderBy('start_date', 'desc')
                ->get();
        }
        
        return view('attachment_selected.index', compact('attachments'));
    }
   
    $attachments = Attachment::orderBy('start_date', 'desc')->get();
    return view('attachment_selected.index', compact('attachments'));
}
public function store(Request $request)
{
    try {
        $request->validate([
            'attachment_id'   => 'required|exists:attachments,id',
            'attachment_name' => 'required',
        ]);

        $user = Auth::user();

        if ($user->role == 'student') {
            $student = Student::where('user_id', $user->id)->first();

            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found.');
            }

            
            $enrollment = AttachmentStudent::updateOrCreate(
                ['student_id' => $student->id], 
                [
                    'attachment_id' => $request->attachment_id,
                    'status' => 'active'
                ]
            );

            session([
                'attachment_id' => $request->attachment_id,
                'attachment_name' => $request->attachment_name,
                'attachment_student_id' => $enrollment->id
            ]);

} elseif ($user->role == 'lecturer') {
    $lecturer = Lecturer::where('user_id', $user->id)->first();
    
    if (!$lecturer) {
        return redirect()->back()->with('error', 'Lecturer profile not found.');
    }

    
    $enrollment = AttachmentLecturer::updateOrCreate(
        ['lecturer_id' => $lecturer->id],
        [
            'attachment_id' => $request->attachment_id,
            'job_grade'     => $lecturer->job_grade,   
            'department_id' => $lecturer->department_id 
        ]
    );

    session([
        'attachment_id' => $request->attachment_id,
        'attachment_name' => $request->attachment_name,
        'attachment_lecturer_id' => $enrollment->id
    ]);

        }if ($user->role == 'industrial_supervisor') {
           
        
            session([
                'attachment_id' => $request->attachment_id,
                'attachment_name' => $request->attachment_name,
            ]);
        }

        return redirect()->route('welcome')->with('success', 'Attachment period selected!');

    } catch (\Exception $e) {
        
        return redirect()->back()->with('error', 'Selection Failed: ' . $e->getMessage());
    }
}
}