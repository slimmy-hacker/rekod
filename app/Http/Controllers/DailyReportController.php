<?php

namespace App\Http\Controllers;

use App\Models\AttachmentStudent;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    /**
     * Display the daily reports calendar view
     */
    public function index(Request $request, $id = null)
    {
        $user_role = Auth::user()->role;
        
        // Get attachment student ID based on role
        if ($user_role == 'student') {
            $attachment_student_id = $request->session()->get('attachment_student_id');
        } elseif ($id) {
            $attachment_student_id = $id;
        } else {
            abort(404);
        }

        // Get daily reports for this attachment student
        $daily_reports = DailyReport::where('attachment_student_id', $attachment_student_id)
                                    ->orderBy('report_date', 'desc')
                                    ->get();

        // Map daily reports to calendar events
        $events = $daily_reports->map(function ($report) {
            $formattedDate = $report->report_date instanceof Carbon 
                ? $report->report_date->format('Y-m-d')
                : date('Y-m-d', strtotime($report->report_date));
            
            return [
                'id' => $report->id,
                'title' => $report->task_title,
                'start' => $formattedDate,
                'end' => $formattedDate,
                'tasks' => $report->tasks,
                'skills_learned' => $report->skills_learned,
                'challenges' => $report->challenges,
                'backgroundColor' => '#3b82f6',
                'textColor' => 'white',
                'extendedProps' => [
                    'daily_report_id' => $report->id,
                    'task_title' => $report->task_title,
                    'tasks' => $report->tasks,
                    'skills_learned' => $report->skills_learned,
                    'challenges' => $report->challenges,
                    'report_date' => $formattedDate
                ]
            ];
        })->values();

        // Get attachment student details
        $attachment_student = AttachmentStudent::with(['student', 'student.user'])
                                    ->where('id', $attachment_student_id)
                                    ->first();

        // Set report route - only students can create reports
        $report_route = '#';
        if ($user_role == 'student') {
            $report_route = route('student.daily_activities.store');
        }

        return view('daily_activities.index', compact('events', 'user_role', 'attachment_student', 'report_route'));
    }

    /**
     * Store or update a daily report
     */
    public function store(Request $request)
    {
        try {
            $attachment_student_id = $request->session()->get('attachment_student_id');
            $attachment_student = AttachmentStudent::find($attachment_student_id);
            
            if (!$attachment_student->company_id ?? null || !$attachment_student->start_date ?? null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fill in your attachment form first.',
                ]);
            }

            // Validate the request
            $validated = $request->validate([
                'daily_report_id' => 'nullable|exists:daily_reports,id',
                'report_date' => [
                    'required',
                    'date',
                    'after_or_equal:' . $attachment_student->start_date,
                    'before_or_equal:' . $attachment_student->end_date,
                ],
                'task_title' => 'required|string|max:255',
                'tasks' => 'required|string',
                'skills_learned' => 'required|string',
                'challenges' => 'nullable|string',
            ]);

            // Prepare data for daily report
            $data = [
                'attachment_student_id' => $attachment_student_id,
                'report_date' => $validated['report_date'],
                'task_title' => $validated['task_title'],
                'tasks' => $validated['tasks'],
                'skills_learned' => $validated['skills_learned'],
                'challenges' => $validated['challenges'] ?? null,
            ];

            // Create or update
            if (!empty($validated['daily_report_id'])) {
                $report = DailyReport::findOrFail($validated['daily_report_id']);
                $report->update($data);
                $daily_report = $report;
            } else {
                $daily_report = DailyReport::create($data);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Daily activity recorded successfully.',
                'data'    => [$this->calendarEventResponse($daily_report->id)]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong. Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format a single daily report for calendar response
     */
    private function calendarEventResponse($id)
    {
        $report = DailyReport::find($id);
        if (!$report) return null;
        
        $formattedDate = $report->report_date instanceof Carbon 
            ? $report->report_date->format('Y-m-d')
            : date('Y-m-d', strtotime($report->report_date));
        
        return [
            'id' => $report->id,
            'title' => $report->task_title,
            'start' => $formattedDate,
            'end' => $formattedDate,
            'tasks' => $report->tasks,
            'skills_learned' => $report->skills_learned,
            'challenges' => $report->challenges,
            'backgroundColor' => '#3b82f6',
            'textColor' => 'white',
            'extendedProps' => [
                'daily_report_id' => $report->id,
                'task_title' => $report->task_title,
                'tasks' => $report->tasks,
                'skills_learned' => $report->skills_learned,
                'challenges' => $report->challenges,
                'report_date' => $formattedDate
            ]
        ];
    }
}