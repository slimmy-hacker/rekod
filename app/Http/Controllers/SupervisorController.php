<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class SupervisorController extends Controller
{
    /**
     * Display a list of students assigned to the logged-in supervisor.
     */
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
