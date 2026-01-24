<?php

namespace App\Http\Controllers;
use App\Models\AttachmentLecturer;
use App\Models\student;
use App\Models\Lecturer;
use App\Models\AttachmentStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    
    /**
     * Display the comprehensive budget for all lecturers.
     * Integrates JobGrade Seeder data and dynamic transport calculations.
     */   
   public function budgets()
{
    // Fetch attachment lecturers joined with lecturers and users to get the name
    $attachment_lecturers = DB::table('attachment_lecturers')
        ->join('lecturers', 'lecturers.id', '=', 'attachment_lecturers.lecturer_id')
        ->join('users', 'users.id', '=', 'lecturers.user_id')
        ->select('attachment_lecturers.*', 'users.name as real_name')
        ->get();

    foreach ($attachment_lecturers as $al) {
        // 1. Fetch Grade Rate
        $gradeData = DB::table('job_grades')
            ->where('dekut_grade', $al->job_grade)
            ->first();

        $rate = $gradeData->daily_allowance ?? 0;

        // 2. Fetch Assignments
        $visits = DB::table('lecturer_assignments')
            ->join('companies', 'companies.id', '=', 'lecturer_assignments.company_id')
            ->where('lecturer_assignments.attachment_lecturer_id', $al->id)
            ->select('companies.address as town', DB::raw('COUNT(*) as students_count'))
            ->groupBy('companies.address')
            ->get();

        // 3. Set properties for the Blade
        // We now use the 'real_name' from the join instead of "Lecturer #ID"
        $al->lecturer_name = $al->real_name; 
        $al->dekut_grade = $al->job_grade ?? 'N/A';
        $al->assessmentVisits = $visits;
        $al->town_count = $visits->count(); 
        $al->daily_rate_used = $rate;
        $al->total_subsistence = $al->town_count * $rate;
        $al->total_transport = $visits->sum(fn($v) => $this->calculateTransport($v->town));
    }

    return view('admin.budgets', compact('attachment_lecturers'));
}
    /**
     * Calculate transport based on the company address string.
     * Matches the logic for specific Kenyan regions.
     */
    private function calculateTransport($address)
    {
        // Normalize the address string for easier matching
        $location = strtolower($address);

        return match (true) {
            str_contains($location, 'nairobi') || str_contains($location, 'upperhill') => 1500,
            str_contains($location, 'nyeri')   => 1000,
            str_contains($location, 'mombasa') => 3000,
            str_contains($location, 'nakuru')  => 2000,
            str_contains($location, 'kisumu')  => 2500,
            default => 800, // Default rate for any other location
        };
    }
}