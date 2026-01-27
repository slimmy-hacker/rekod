<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeUnit;
use App\Models\Attachment;
use App\Models\AttachmentLecturer;
use App\Models\AttachmentStudent;
use App\Models\LecturerAssigment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LecturerAssigmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AttachmentStudent::whereNotNull('company_id')
                ->with([
                    'attachment',
                    'student.user',
                    'student.program.parent', // This is the department
                    'attachmentLecturer.lecturer.user',
                    'company.town'
                ]);

            if (!empty($request->attachment_id)) {
                $data->where('attachment_id', $request->attachment_id);
            }

            if (!empty($request->department_id)) {
                $data->whereHas('student.program.parent', function ($q) use ($request) {
                    $q->where('id', $request->department_id);
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->student->user->name ?? '-')
                ->addColumn('reg_no', fn($row) => $row->student->reg_no ?? '-')
                ->addColumn('attachment', fn($row) => $row->attachment->name ?? '-')
                  ->addColumn('phone_number', fn ($row) =>  $row->student->phone_number ?? '-')
                ->addColumn('department', fn($row) => $row->student->program->parent->name ?? '-')
                ->addColumn('lecturer', function ($row) {
                    if ($row->attachmentLecturer && $row->attachmentLecturer->lecturer->user) {
                        return $row->attachmentLecturer->lecturer->user->name;
                    }
                    return $row->attachment_lecturer_id ? 'Assigned (ID: '.$row->attachment_lecturer_id.')' : '<span class="badge badge-warning">Not Assigned</span>';
                })
                ->addColumn('company', fn($row) => $row->company->name ?? '-')
                ->addColumn('town', fn($row) => $row->company->town->name ?? '-')
                ->rawColumns(['lecturer'])
                ->make(true);
        }

        $attachments = Attachment::select('id', 'name')->orderBy('created_at', 'desc')->get();
        $departments = AdministrativeUnit::where('level', 2)->get();

        return view('admin.lecturer_assignment', compact('attachments', 'departments'));
    }

    /**
     * Calculate Distance using Haversine Formula
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Proximity-based Assignment Logic (Greedy Clustering)
     */
  public function generateDraft(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:administrative_units,id',
        'attachment_id' => 'required|exists:attachments,id',
    ]);

    $regionalRange = 60.0; 
    $maxStudents = 10;

    // 1. Filter at the DATABASE level to ensure the relationship chain is complete
    // This stops the "Attempt to read property on null" because we only pull records where town exists.
    $rawStudents = AttachmentStudent::with(['company.town'])
        ->where('attachment_id', $request->attachment_id)
        ->whereNull('attachment_lecturer_id')
        // Ensure company exists
        ->whereHas('company', function($q) {
            // Ensure town exists for that company
            $q->whereHas('town'); 
        })
        ->whereHas('student.program.parent', function($q) use ($request) {
            $q->where('id', $request->department_id);
        })
        ->get();

    if ($rawStudents->isEmpty()) {
        return response()->json([
            'status' => 'error', 
            'message' => 'No unassigned students found with complete location data (Company + Town).'
        ]);
    }

    // 2. Safe mapping with coordinates
    $students = $rawStudents->map(function ($s) {
        // Since we used whereHas, we know these exist
        $s->lat = (float) $s->company->town->latitude;
        $s->lng = (float) $s->company->town->longitude;
        return $s;
    })
    ->sortBy('lat') 
    ->values();

    // 3. Fetch Lecturers
    $lecturers = AttachmentLecturer::where([
        'attachment_id' => $request->attachment_id,
        'department_id' => $request->department_id
    ])->get();

    if ($lecturers->isEmpty()) {
        return response()->json(['status' => 'error', 'message' => 'No lecturers available.']);
    }

    $assignedIds = [];
    $finalAssignments = [];

    // 4. Regional Grouping Loop (Bomet, Kericho, etc.)
    foreach ($lecturers as $lecturer) {
        $remaining = $students->whereNotIn('id', $assignedIds);
        if ($remaining->isEmpty()) break;

        $anchor = $remaining->first();

        $regionalGroup = $remaining->map(function ($target) use ($anchor) {
            $target->dist = $this->haversineDistance($anchor->lat, $anchor->lng, $target->lat, $target->lng);
            return $target;
        })
        ->filter(fn($t) => $t->dist <= $regionalRange)
        ->sortBy('dist')
        ->take($maxStudents);

        foreach ($regionalGroup as $student) {
            $finalAssignments[] = [
                'student_id' => $student->id,
                'lecturer_id' => $lecturer->id,
                'company_id'  => $student->company_id,
                'lat'         => $student->lat,
                'lng'         => $student->lng
            ];
            $assignedIds[] = $student->id;
        }
    }

    // 5. Save using Transaction
    return DB::transaction(function () use ($finalAssignments, $request) {
        $batch = (LecturerAssigment::where(['attachment_id' => $request->attachment_id])->max('batch') ?? 0) + 1;

        foreach ($finalAssignments as $item) {
            AttachmentStudent::where('id', $item['student_id'])->update([
                'attachment_lecturer_id' => $item['lecturer_id']
            ]);

            LecturerAssigment::create([
                'attachment_id' => $request->attachment_id,
                'department_id' => $request->department_id,
                'attachment_student_id' => $item['student_id'],
                'attachment_lecturer_id' => $item['lecturer_id'],
                'company_id' => $item['company_id'],
                'latitude' => $item['lat'],
                'longitude' => $item['lng'],
                'batch' => $batch,
                'created_by' => auth()->id(),
            ]);
        }
        return response()->json(['status' => 'success', 'message' => count($finalAssignments) . " students assigned to regional clusters."]);
    });
}
}