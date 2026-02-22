<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\IndustrialSupervisor;
use App\Models\Company;
use App\Models\AttachmentStudent;
use App\Models\DailyReport;
use App\Models\AttachmentAssessment;
use App\Models\Location;
use App\Models\WeeklyReport;
use App\Models\FinalReport; // Add this if you have a FinalReport model
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current year for trends
        $currentYear = date('Y');
        
        // Total counts
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalIndustrialSupervisors = IndustrialSupervisor::count();
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $totalTowns = Location::where('level', 3)->count();
        $totalDailyReports = DailyReport::count();
        $totalAssessments = AttachmentAssessment::count();
        $totalWeeklyReports = WeeklyReport::count(); // Total weekly reports
        $totalFinalReports = FinalReport::count(); // Add this if you have FinalReport model
        
        // Weekly reports count (last 7 days)
        $weeklyReportsThisWeek = WeeklyReport::where('created_at', '>=', now()->subDays(7))->count();
        
        // Monthly trends for the chart
        $monthlyTrends = $this->getMonthlyTrends($currentYear);
        
        // Get student locations for map
        $studentLocations = $this->getStudentLocations();
        
        // Department distribution
        $departmentDistribution = $this->getDepartmentDistribution();
        
        // Top companies by student count
        $topCompanies = $this->getTopCompanies();
        
  // Recent Daily Reports - Fixed to use weekly_report_id
$recentDailyReports = DailyReport::latest()
    ->take(5)
    ->get()
    ->map(function($report) {
        $student = null;
        
        // Get the weekly report first
        if (isset($report->weekly_report_id)) {
            $weeklyReport = DB::table('weekly_reports')
                ->where('id', $report->weekly_report_id)
                ->first();
            
            if ($weeklyReport) {
                // Try to find student through weekly report's fields
                if (isset($weeklyReport->attachment_student_id)) {
                    $attachment = DB::table('attachment_students')
                        ->where('id', $weeklyReport->attachment_student_id)
                        ->first();
                    if ($attachment) {
                        $student = DB::table('students')
                            ->where('id', $attachment->student_id)
                            ->first();
                    }
                }
                
                if (!$student && isset($weeklyReport->student_id)) {
                    $student = DB::table('students')
                        ->where('id', $weeklyReport->student_id)
                        ->first();
                }
                
                if (!$student && isset($weeklyReport->user_id)) {
                    $student = DB::table('students')
                        ->where('user_id', $weeklyReport->user_id)
                        ->first();
                }
            }
        }
        
        if ($student) {
            $user = DB::table('users')->where('id', $student->user_id)->first();
            $report->student_name = $user->name ?? 'Unknown';
            $report->reg_no = $student->reg_no ?? 'N/A';
        } else {
            $report->student_name = 'Daily Report #' . $report->id;
            $report->reg_no = 'N/A';
        }
        
        return $report;
    });

// Recent Weekly Reports
$recentWeeklyReports = WeeklyReport::latest()
    ->take(5)
    ->get()
    ->map(function($report) {
        // Try to find the student by ID first
        $student = null;
        
        if (isset($report->student_id)) {
            $student = DB::table('students')
                ->where('id', $report->student_id)
                ->first();
        }
        
        if (!$student && isset($report->user_id)) {
            $student = DB::table('students')
                ->where('user_id', $report->user_id)
                ->first();
        }
        
        if (!$student && isset($report->attachment_student_id)) {
            $attachment = DB::table('attachment_students')
                ->where('id', $report->attachment_student_id)
                ->first();
            if ($attachment) {
                $student = DB::table('students')
                    ->where('id', $attachment->student_id)
                    ->first();
            }
        }
        
        if ($student) {
            $user = DB::table('users')->where('id', $student->user_id)->first();
            $report->student_name = $user->name ?? 'Unknown';
            $report->reg_no = $student->reg_no ?? 'N/A';
        } else {
            $report->student_name = 'Unknown Student';
            $report->reg_no = 'N/A';
        }
        
        return $report;
    });
    // Recent Assessments - Include more fields
$recentAssessments = AttachmentAssessment::with('attachmentStudent.student.user')
    ->latest()
    ->take(5)
    ->get()
    ->map(function($assessment) {
        $studentName = 'Unknown Student';
        
        if ($assessment->attachmentStudent && 
            $assessment->attachmentStudent->student && 
            $assessment->attachmentStudent->student->user) {
            $studentName = $assessment->attachmentStudent->student->user->name;
        }
        
        $assessment->student_name = $studentName;
        
        // Set default values if fields don't exist
        $assessment->assessment_name = $assessment->assessment_name ?? null;
        $assessment->score = $assessment->score ?? null;
        $assessment->grade = $assessment->grade ?? null;
        
        return $assessment;
    });
        // ===== ADD THIS SECTION - Recent Final Reports =====
        $recentFinalReports = FinalReport::with('attachmentStudent.student.user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($report) {
                $studentName = 'Unknown Student';
                
                if ($report->attachmentStudent && 
                    $report->attachmentStudent->student && 
                    $report->attachmentStudent->student->user) {
                    $studentName = $report->attachmentStudent->student->user->name;
                }
                
                $report->student_name = $studentName;
                return $report;
            });
        // ===============
        // Supervisor statistics
        $supervisorStats = $this->getSupervisorStats();
        
        // Attachment statistics
        $attachmentStats = $this->getAttachmentStats();
        
        return view('dashboard', compact(
            'totalStudents',
            'totalLecturers',
            'totalIndustrialSupervisors',
            'totalUsers',
            'totalCompanies',
            'totalTowns',
            'totalDailyReports',
            'totalAssessments',
            'totalWeeklyReports',
            'weeklyReportsThisWeek', // Add this
            'totalFinalReports', // Add this if you have it
            'monthlyTrends',
            'studentLocations',
            'departmentDistribution',
            'topCompanies',
            'recentDailyReports',
            'recentWeeklyReports',
            'recentFinalReports',
            'supervisorStats',
             'recentAssessments',
            'attachmentStats',
            'currentYear'
        ));
    }
    private function getMonthlyTrends($year)
{
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $trends = [
        'labels' => $months,
        'daily_reports' => [],
        'weekly_reports' => [],
        'final_reports' => [], // ADDED
        'students' => [],
        'attachments' => [],
        'assessments' => []
    ];
    
    // Check if there's any data at all
    $hasDailyData = DailyReport::exists();
    $hasWeeklyData = WeeklyReport::exists();
    $hasFinalData = FinalReport::exists(); // ADDED
    
    for ($month = 1; $month <= 12; $month++) {
        // Daily Reports
        if ($hasDailyData) {
            $dailyCount = DailyReport::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        } else {
            $dailyCount = rand(2, 8);
        }
        
        // Weekly Reports
        if ($hasWeeklyData) {
            $weeklyCount = WeeklyReport::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        } else {
            $weeklyCount = rand(1, 4);
        }
        
        // FINAL REPORTS - ADDED
        if ($hasFinalData) {
            $finalCount = FinalReport::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        } else {
            $finalCount = rand(0, 2); // Sample data for testing
        }
        
        $studentsCount = Student::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        
        $attachmentsCount = AttachmentStudent::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        
        $assessmentsCount = AttachmentAssessment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
        
        $trends['daily_reports'][] = $dailyCount;
        $trends['weekly_reports'][] = $weeklyCount;
        $trends['final_reports'][] = $finalCount; // ADDED
        $trends['students'][] = $studentsCount;
        $trends['attachments'][] = $attachmentsCount;
        $trends['assessments'][] = $assessmentsCount;
    }
    
    return $trends;
}
    private function getStudentLocations()
{
    return AttachmentStudent::with(['company.town', 'student.user'])
        ->whereHas('company', function($q) {
            $q->whereNotNull('town_id');
        })
        ->whereHas('company.town', function($q) {
            $q->whereNotNull('latitude')
              ->whereNotNull('longitude')
              ->where('latitude', '!=', 0)
              ->where('longitude', '!=', 0);
        })
        ->get()
        ->map(function($attachment) {
            $lat = (float) $attachment->company->town->latitude;
            $lng = (float) $attachment->company->town->longitude;
            
            // Skip if coordinates are invalid
            if ($lat == 0 || $lng == 0 || abs($lat) > 90 || abs($lng) > 180) {
                return null;
            }
            
            return [
                'student_name' => $attachment->student->user->name ?? 'Unknown',
                'company_name' => $attachment->company->name ?? 'Unknown',
                'town' => $attachment->company->town->name ?? 'Unknown',
                'lat' => $lat,
                'lng' => $lng,
                'reg_no' => $attachment->student->reg_no ?? 'N/A'
            ];
        })
        ->filter() // Remove null values
        ->values(); // Reset array keys
}
    private function getDepartmentDistribution()
    {
        try {
            // Try to get department distribution from students table
            $students = Student::select('department_id')
                ->selectRaw('count(*) as total')
                ->groupBy('department_id')
                ->get();
            
            if ($students->isNotEmpty()) {
                return $students->map(function($item) {
                    return (object)[
                        'name' => 'Department ' . $item->department_id,
                        'total' => $item->total
                    ];
                });
            }
            
            // Fallback
            return collect([
                (object)['name' => 'All Students', 'total' => Student::count()]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in department distribution: ' . $e->getMessage());
            return collect([
                (object)['name' => 'All Students', 'total' => Student::count()]
            ]);
        }
    }private function getTopCompanies()
{
    try {
        return DB::table('companies')
            ->leftJoin('attachment_students', 'companies.id', '=', 'attachment_students.company_id')
            ->leftJoin('locations', 'companies.town_id', '=', 'locations.id')
            ->select(
                'companies.id',
                'companies.name',
                'locations.name as town_name',
                DB::raw('COUNT(attachment_students.id) as students_count')
            )
            ->whereNotNull('attachment_students.company_id')
            ->groupBy('companies.id', 'companies.name', 'locations.name')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get()
            ->map(function($company) {
                // If town_name is null, set it to 'Unknown Location'
                $company->town_name = $company->town_name ?? 'Unknown Location';
                return $company;
            });
            
    } catch (\Exception $e) {
        \Log::error('Error in top companies: ' . $e->getMessage());
        return collect([]);
    }
}
    
    private function getSupervisorStats()
    {
        try {
            $total = IndustrialSupervisor::count();
            
            // Active supervisors (those with company_id)
            $active = IndustrialSupervisor::whereNotNull('company_id')->count();
            
            // Supervisors with students (through attachment_students)
            $with_students = DB::table('industrial_supervisors')
                ->join('attachment_students', 'industrial_supervisors.id', '=', 'attachment_students.industrial_supervisor_id')
                ->distinct('industrial_supervisors.id')
                ->count('industrial_supervisors.id');
            
            // Companies covered
            $companies_count = IndustrialSupervisor::whereNotNull('company_id')
                ->distinct('company_id')
                ->count('company_id');
            
            return [
                'total' => $total,
                'active' => $active,
                'with_students' => $with_students,
                'companies_count' => $companies_count
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error in supervisor stats: ' . $e->getMessage());
            
            return [
                'total' => IndustrialSupervisor::count(),
                'active' => 0,
                'with_students' => 0,
                'companies_count' => 0
            ];
        }
    }
    
    private function getAttachmentStats()
    {
        $total = AttachmentStudent::count();
        
        // You'll need to add a 'status' column to attachment_students or determine status based on dates
        $ongoing = AttachmentStudent::whereNull('end_date')
            ->orWhere('end_date', '>', now())
            ->count();
        
        $completed = AttachmentStudent::whereNotNull('end_date')
            ->where('end_date', '<=', now())
            ->count();
        
        $pending = AttachmentStudent::whereNull('attachment_lecturer_id')->count();
        
        return [
            'total' => $total,
            'ongoing' => $ongoing,
            'completed' => $completed,
            'pending' => $pending
        ];
    }
    public function generateReport(Request $request)
{
    $request->validate([
        'report_type' => 'required|in:students,daily,weekly,final,companies,attachments,supervisors,assessments',
        'date_range' => 'nullable|string',
        'format' => 'required|in:pdf'
    ]);

    // Get data based on report type
    $data = $this->getReportData($request->report_type);
    $type = $request->report_type;
    
    // Create PDF using Blade view
    $pdf = \PDF::loadView('reports.' . $type, compact('data'));
    
    // Download PDF
    return $pdf->download($type . '-report-' . date('Y-m-d-His') . '.pdf');
}

private function getReportData($type)
{
    switch ($type) {
        case 'students':
            return Student::with('user', 'program')->get();
        case 'daily':
            return DailyReport::with('student.user')->get();
        case 'weekly':
            return WeeklyReport::with('student.user')->get();
        case 'final':
            return FinalReport::with('attachmentStudent.student.user')->get();
        case 'companies':
            return Company::with('town', 'attachmentStudents')->get();
        case 'attachments':
            return AttachmentStudent::with('student.user', 'company', 'lecturer.user', 'industrialSupervisor.user')->get();
        case 'supervisors':
            return IndustrialSupervisor::with('user', 'company', 'students')->get();
        case 'assessments':
            return AttachmentAssessment::with('attachmentStudent.student.user')->get();
        default:
            return collect([]);
    }
}
}