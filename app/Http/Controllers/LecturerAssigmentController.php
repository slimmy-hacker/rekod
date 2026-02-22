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
                    'student.program.parent', 
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
public function generateDraft(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:administrative_units,id',
        'attachment_id' => 'required|exists:attachments,id',
    ]);

    // 1. Fetch students with company and town relations
    $students = AttachmentStudent::with(['company.town', 'student.program.parent'])
        ->where('attachment_id', $request->attachment_id)
        ->whereNotNull('company_id')
        ->whereHas('student.program.parent', fn($q) => $q->where('id', $request->department_id))
        ->get();

    if ($students->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No students found for this attachment and department.'
        ]);
    }

    
    $towns = [];
    $studentsById = [];

    foreach ($students as $student) {
        if (!$student->company || !$student->company->town) continue;

        $town = $student->company->town;
        if (is_null($town->latitude) || is_null($town->longitude)) continue;

        $townId = $town->id;
        
        if (!isset($towns[$townId])) {
            $towns[$townId] = [
                'town_id' => $townId,
                'town_name' => $town->name,
                'latitude' => (float) $town->latitude,
                'longitude' => (float) $town->longitude,
                'students' => [],
                'student_count' => 0,
                'student_ids' => []
            ];
        }

        $studentData = (object) [
            'id' => $student->id,
            'company_id' => $student->company_id,
            'attachment_id' => $student->attachment_id,
            'student_id' => $student->student_id,
            'lat' => (float) $town->latitude,
            'lng' => (float) $town->longitude,
            'town_id' => $townId,
            'town_name' => $town->name,
            'original_model' => $student
        ];

        $towns[$townId]['students'][] = $studentData;
        $towns[$townId]['student_ids'][] = $student->id;
        $towns[$townId]['student_count']++;
        $studentsById[$student->id] = $studentData;
    }

    if (empty($towns)) {
        return response()->json([
            'status' => 'error',
            'message' => 'No students with valid coordinates found in company.town.'
        ]);
    }

    
    $lecturers = AttachmentLecturer::with('lecturer.user')
        ->where([
            'attachment_id' => $request->attachment_id,
            'department_id' => $request->department_id
        ])
        ->get();
        

    if ($lecturers->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No lecturers assigned for this attachment and department.'
        ]);
    }

    
   $totalStudents = collect($towns)->sum('student_count');
    $totalLecturers = $lecturers->count();
    
  
    $BASE_TARGET = (int) floor($totalStudents / $totalLecturers);
    $REMAINDER = $totalStudents % $totalLecturers;
    $MAX_STUDENTS_PER_LECTURER = (int) ceil($totalStudents / $totalLecturers);
    
   
    $MIN_STUDENTS_PER_LECTURER = 5;  // Minimum practical number
    $MAX_CAP = 15;  // Maximum you want per lecturer
    
    // Apply constraints if needed
    $MAX_STUDENTS_PER_LECTURER = max($MIN_STUDENTS_PER_LECTURER, 
                                    min($MAX_CAP, $MAX_STUDENTS_PER_LECTURER));
    $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS = 70;
    
    $totalStudents = collect($towns)->sum('student_count');
    
    
    $townList = array_values($towns);
    usort($townList, fn($a, $b) => $b['student_count'] <=> $a['student_count']);
    
    $studentGroups = [];
    
    foreach ($townList as $town) {
        $townStudentIds = $town['student_ids'];
        $townLat = $town['latitude'];
        $townLng = $town['longitude'];
        $townName = $town['town_name'];
        
        
        $chunks = array_chunk($townStudentIds, $MAX_STUDENTS_PER_LECTURER);
        foreach ($chunks as $index => $chunk) {
            $studentGroups[] = [
                'id' => 'group_' . count($studentGroups),
                'name' => count($chunks) > 1 ? $townName . ' (Group ' . ($index + 1) . ')' : $townName,
                'student_ids' => $chunk,
                'student_count' => count($chunk),
                'latitude' => $townLat,
                'longitude' => $townLng,
                'town' => $townName
            ];
        }
    }
   
$smallGroups = [];
$largeGroups = [];

foreach ($studentGroups as $group) {
    if ($group['student_count'] <= 3) { 
        $smallGroups[] = $group;
    } else {
        $largeGroups[] = $group;
    }
}


usort($smallGroups, fn($a, $b) => $a['student_count'] <=> $b['student_count']);

usort($largeGroups, fn($a, $b) => $b['student_count'] <=> $a['student_count']);


$studentGroups = array_merge($smallGroups, $largeGroups);


    
   
    $lecturerAssignments = [];
    $defaultLat = -0.0236;
$defaultLng = 37.9062;
    foreach ($lecturers as $index => $lecturer) {
        $target = $BASE_TARGET + ($index < $REMAINDER ? 1 : 0);
        
        $lecturerAssignments[$lecturer->id] = [
            'lecturer_id' => $lecturer->id,
            'lecturer_name' => $lecturer->lecturer->user->name ?? 'Unknown',
            'student_ids' => [],
            'student_count' => 0,
            'target' => $target,
            'deficit' => $target, // How many more needed to reach target
            'groups' => [],
            'towns' => [], 
            'latitude' => $lecturer->latitude ?? $defaultLat,  
            'longitude' => $lecturer->longitude ?? $defaultLng, 
        ];
    }
    
   
    $unassignedGroups = [];
    
    // First pass - try to assign all groups with strict proximity
    foreach ($studentGroups as $group) {
        $groupLat = $group['latitude'];
        $groupLng = $group['longitude'];
        $groupSize = $group['student_count'];
        $groupTown = $group['town'];
        
        $eligibleLecturers = [];
        
        foreach ($lecturerAssignments as $lecturerId => $assignment) {
            
            if ($assignment['student_count'] + $groupSize > $MAX_STUDENTS_PER_LECTURER) {
                continue;
            }
            
           
$canAssign = true;

if (!empty($assignment['towns'])) {
    
    $allTowns = $assignment['towns'];
    $allTowns[$groupTown] = [
        'lat' => $groupLat,
        'lng' => $groupLng
    ];
    
    
    $townNames = array_keys($allTowns);
    for ($i = 0; $i < count($townNames); $i++) {
        for ($j = $i + 1; $j < count($townNames); $j++) {
            $town1 = $townNames[$i];
            $town2 = $townNames[$j];
            
            $distance = $this->haversineDistance(
                $allTowns[$town1]['lat'],
                $allTowns[$town1]['lng'],
                $allTowns[$town2]['lat'],
                $allTowns[$town2]['lng']
            );
            
            
            if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                $canAssign = false;
                break 2;
            }
        }
    }
}

if (!$canAssign) {
    continue;
}
            
            
            $distanceToLecturer = $this->haversineDistance(
                $groupLat, $groupLng,
                $assignment['latitude'], $assignment['longitude']
            );
            
            $deficit = $assignment['target'] - $assignment['student_count'];
            
            // NEW SCORING - prioritize deficit heavily
            $eligibleLecturers[] = [
                'lecturer_id' => $lecturerId,
                'distance_to_lecturer' => $distanceToLecturer,
                'current_load' => $assignment['student_count'],
                'deficit' => $deficit,
                'has_same_town' => isset($assignment['towns'][$groupTown]),
                'priority' => ($deficit * 100) + (isset($assignment['towns'][$groupTown]) ? 30 : 0) - ($distanceToLecturer / 10)
            ];
        }
        
       
        if (empty($eligibleLecturers)) {
            // Can't assign now - save for second pass with relaxed capacity
            $unassignedGroups[] = $group;
            continue;
        }
        
       // Sort by priority (HIGHEST first)
        usort($eligibleLecturers, fn($a, $b) => $b['priority'] <=> $a['priority']);
        
       
        $bestLecturerId = $eligibleLecturers[0]['lecturer_id'];
        
        
        $lecturerAssignments[$bestLecturerId]['student_ids'] = array_merge(
            $lecturerAssignments[$bestLecturerId]['student_ids'],
            $group['student_ids']
        );
        $lecturerAssignments[$bestLecturerId]['student_count'] += $groupSize;
        $lecturerAssignments[$bestLecturerId]['groups'][] = $group['name'];
        $lecturerAssignments[$bestLecturerId]['towns'][$groupTown] = [
            'lat' => $groupLat,
            'lng' => $groupLng
        ];
        $lecturerAssignments[$bestLecturerId]['latitude'] = $groupLat;
        $lecturerAssignments[$bestLecturerId]['longitude'] = $groupLng;
    }
    
    // Second pass - handle unassigned groups with relaxed capacity but STRICT proximity
    if (!empty($unassignedGroups)) {
        \Log::info("Second pass: " . count($unassignedGroups) . " groups to assign");
        
        // Break groups into individual students for more flexibility
        $individualStudents = [];
        foreach ($unassignedGroups as $group) {
            foreach ($group['student_ids'] as $studentId) {
                $individualStudents[] = [
                    'student_id' => $studentId,
                    'town' => $group['town'],
                    'latitude' => $group['latitude'],
                    'longitude' => $group['longitude']
                ];
            }
        }
        
        // Sort lecturers by current load (lowest first) for fairness
        $lecturersByLoad = array_keys($lecturerAssignments);
        usort($lecturersByLoad, fn($a, $b) => 
            $lecturerAssignments[$a]['student_count'] <=> $lecturerAssignments[$b]['student_count']
        );
        
        // Assign each student individually, prioritizing fair distribution
        foreach ($individualStudents as $student) {
            $town = $student['town'];
            $lat = $student['latitude'];
            $lng = $student['longitude'];
            
            $eligibleLecturers = [];
            
            foreach ($lecturersByLoad as $lecturerId) {
                $assignment = $lecturerAssignments[$lecturerId];
                
                // Relax capacity for second pass
                if ($assignment['student_count'] + 1 > $MAX_STUDENTS_PER_LECTURER + 2) {
                    continue;
                }
                
                // STRICT PROXIMITY CHECK - still enforced
                $canAssign = true;
                
                if (!empty($assignment['towns'])) {
                    $allTowns = $assignment['towns'];
                    $allTowns[$town] = ['lat' => $lat, 'lng' => $lng];
                    
                    $townNames = array_keys($allTowns);
                    for ($i = 0; $i < count($townNames); $i++) {
                        for ($j = $i + 1; $j < count($townNames); $j++) {
                            $town1 = $townNames[$i];
                            $town2 = $townNames[$j];
                            
                            $distance = $this->haversineDistance(
                                $allTowns[$town1]['lat'],
                                $allTowns[$town1]['lng'],
                                $allTowns[$town2]['lat'],
                                $allTowns[$town2]['lng']
                            );
                            
                            if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                                $canAssign = false;
                                break 2;
                            }
                        }
                    }
                }
                
                if (!$canAssign) continue;
                
                $eligibleLecturers[] = $lecturerId;
            }
            
            if (!empty($eligibleLecturers)) {
                // Assign to lecturer with lowest current load among eligible
                $bestLecturerId = $eligibleLecturers[0]; // Already sorted by load
                
                $lecturerAssignments[$bestLecturerId]['student_ids'][] = $student['student_id'];
                $lecturerAssignments[$bestLecturerId]['student_count']++;
                if (!isset($lecturerAssignments[$bestLecturerId]['towns'][$town])) {
                    $lecturerAssignments[$bestLecturerId]['towns'][$town] = [
                        'lat' => $lat,
                        'lng' => $lng
                    ];
                }
                
                // Re-sort lecturers by load
                usort($lecturersByLoad, fn($a, $b) => 
                    $lecturerAssignments[$a]['student_count'] <=> $lecturerAssignments[$b]['student_count']
                );
            }
        }
    }
    
    // Verify all students are assigned
    $allStudentIds = collect($towns)->flatMap(fn($t) => $t['student_ids'])->toArray();
    $assignedIds = [];
    foreach ($lecturerAssignments as $assignment) {
        $assignedIds = array_merge($assignedIds, $assignment['student_ids']);
    }
    
    $missingIds = array_diff($allStudentIds, $assignedIds);
    
    if (!empty($missingIds)) {
        \Log::warning(count($missingIds) . " students still unassigned after all passes");
    }
    
    $violations = [];
    $loadDistribution = [];
    foreach ($lecturerAssignments as $lecturerId => $assignment) {
        $loadDistribution[] = $assignment['student_count'];
        
        if ($assignment['student_count'] > $MAX_STUDENTS_PER_LECTURER + 2) {
            $violations[] = "{$assignment['lecturer_name']} has {$assignment['student_count']} students (max " . ($MAX_STUDENTS_PER_LECTURER + 2) . ")";
        }
        
        
        if (count($assignment['towns']) > 1) {
            $towns = array_keys($assignment['towns']);
            for ($i = 0; $i < count($towns); $i++) {
                for ($j = $i + 1; $j < count($towns); $j++) {
                    $town1 = $towns[$i];
                    $town2 = $towns[$j];
                    
                    $distance = $this->haversineDistance(
                        $assignment['towns'][$town1]['lat'],
                        $assignment['towns'][$town1]['lng'],
                        $assignment['towns'][$town2]['lat'],
                        $assignment['towns'][$town2]['lng']
                    );
                    
                    if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                        $violations[] = "{$assignment['lecturer_name']} mixed $town1 and $town2 (" . round($distance) . "km apart)";
                    }
                }
            }
        }
    }
    
   
    DB::transaction(function () use ($lecturerAssignments, $request, $studentsById) {
        $prevBatch = LecturerAssigment::where([
            'attachment_id' => $request->attachment_id,
            'department_id' => $request->department_id
        ])->orderBy('batch', 'desc')->first();

        $batch = $prevBatch ? $prevBatch->batch + 1 : 1;

        foreach ($lecturerAssignments as $assignment) {
            if (empty($assignment['student_ids'])) continue;
            
            $lecturerId = $assignment['lecturer_id'];
            
            foreach ($assignment['student_ids'] as $studentId) {
                $studentData = $studentsById[$studentId] ?? null;
                if (!$studentData) continue;

                AttachmentStudent::where('id', $studentId)->update([
                    'attachment_lecturer_id' => $lecturerId
                ]);

                LecturerAssigment::create([
                    'attachment_id' => $request->attachment_id,
                    'department_id' => $request->department_id,
                    'attachment_student_id' => $studentId,
                    'attachment_lecturer_id' => $lecturerId,
                    'latitude' => $studentData->lat,
                    'longitude' => $studentData->lng,
                    'company_id' => $studentData->company_id,
                    'batch' => $batch,
                    'created_by' => auth()->id(),
                ]);
            }
        }
    });

    
    $distribution = [];
    $lecturerNames = [];
    $loadDetails = [];
    
    foreach ($lecturerAssignments as $a) {
        if ($a['student_count'] > 0) {
            $distribution[] = $a['student_count'];
            $lecturerNames[] = $a['lecturer_name'];
            $loadDetails[$a['lecturer_name']] = [
                'students' => $a['student_count'],
                'target' => $a['target'] ?? $BASE_TARGET,
                'towns' => array_keys($a['towns'])
            ];
        }
    }

    $assignedCount = count($assignedIds ?? []);
    if (empty($assignedIds)) {
        $assignedCount = collect($lecturerAssignments)->sum('student_count');
    }

    $response = [
        'status' => empty($violations) ? 'success' : 'warning',
        'message' => "{$assignedCount} of {$totalStudents} students assigned to " . count($distribution) . " lecturers.",
        'distribution' => array_combine($lecturerNames, $distribution),
        'load_details' => $loadDetails,
        'fairness_metrics' => [
            'min_load' => !empty($loadDistribution) ? min($loadDistribution) : 0,
            'max_load' => !empty($loadDistribution) ? max($loadDistribution) : 0,
            'average' => !empty($loadDistribution) ? round(array_sum($loadDistribution) / count($loadDistribution), 1) : 0,
            'target_per_lecturer' => $BASE_TARGET
        ],
        'constraints' => [
            'max_students_per_lecturer' => $MAX_STUDENTS_PER_LECTURER,
            'max_distance_between_towns' => $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS . 'km'
        ]
    ];
    
    if (!empty($violations)) {
        $response['warnings'] = $violations;
    }
    
    return response()->json($response);
}

private function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; 
    
    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);
    
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;
    
    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    
    return $angle * $earthRadius;
}
}
