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
{public function index(Request $request)
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

      
        $students = $data->get();
        
        
        $groupedData = [];
        $counter = 1;
        
        
        $lecturerGroups = [];
        foreach ($students as $student) {
            
            $lecturerName = 'Unknown';
            if ($student->attachmentLecturer && 
                $student->attachmentLecturer->lecturer && 
                $student->attachmentLecturer->lecturer->user) {
                $lecturerName = $student->attachmentLecturer->lecturer->user->name;
            } else {
                $lecturerName = '<span class="badge badge-warning">Not Assigned</span>';
            }
            
            if (!isset($lecturerGroups[$lecturerName])) {
                $lecturerGroups[$lecturerName] = [];
            }
            $lecturerGroups[$lecturerName][] = $student;
        }
        
        
        uksort($lecturerGroups, function($a, $b) {
            if ($a === '<span class="badge badge-warning">Not Assigned</span>') return 1;
            if ($b === '<span class="badge badge-warning">Not Assigned</span>') return -1;
            return strcmp($a, $b);
        });
        
        
        foreach ($lecturerGroups as $lecturerName => $students) {
            
            usort($students, function($a, $b) {
                return strcmp($a->student->user->name ?? '', $b->student->user->name ?? '');
            });
            
            foreach ($students as $student) {
                $groupedData[] = [
                    'DT_RowIndex' => $counter++,
                    'attachment' => $student->attachment->name ?? '-',
                    'name' => $student->student->user->name ?? '-',
                    'reg_no' => $student->student->reg_no ?? '-',
                    'department' => $student->student->program->parent->name ?? '-',
                    'lecturer' => $lecturerName,
                    'company' => $student->company->name ?? '-',
                    'town' => $student->company->town->name ?? '-',
                    'phone_number' => $student->student->phone_number ?? '-'
                ];
            }
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => count($groupedData),
            'recordsFiltered' => count($groupedData),
            'data' => $groupedData
        ]);
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
    
   
    $MIN_STUDENTS_PER_LECTURER = 5;  
    $MAX_CAP = 15;  
    $MAX_STUDENTS_PER_LECTURER = max($MIN_STUDENTS_PER_LECTURER, 
                                    min($MAX_CAP, $MAX_STUDENTS_PER_LECTURER));
    $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS = 72;
  

$specialClusters = [
    'nakuru_cluster' => [
        'towns' => ['Nakuru', 'Naivasha', 'Nyahururu', 'Gilgil', 'Molo', 'Njoro', 'Elementaita'],
        'max_distance' => 100 
    ],
    'kisumu_cluster' => [
        'towns' => ['Kisumu', 'Kakamega', 'Vihiga', 'Ahero', 'Muhoroni', 'Maseno'],
        'max_distance' => 80 
    ],
    'nairobi_metro' => [
        'towns' => ['Nairobi', 'Kiambu', 'Thika', 'Juja', 'Ruiru', 'Kikuyu', 'Kitengela', 'Ongata Rongai', 'Athi River'],
        'max_distance' => 50 
    ],
    'nyeri_cluster' => [
        'towns' => ['Nyeri', 'Karatina', 'Kutus', 'Kerugoya', 'Nanyuki'],
        'max_distance' => 60 
    ]
];

function findTownCluster($townName, $clusters) {
    foreach ($clusters as $clusterKey => $cluster) {
        if (in_array($townName, $cluster['towns'])) {
            return $clusterKey;
        }
    }
    return null;
}

function isNearClusterTown($lat, $lng, $clusterTowns, $townsData, $haversineFunction, $maxDistance) {
    foreach ($clusterTowns as $clusterTown) {
        foreach ($townsData as $town) {
            if ($town['town_name'] === $clusterTown) {
                $distance = $haversineFunction(
                    $lat, $lng,
                    $town['latitude'], $town['longitude']
                );
                if ($distance <= $maxDistance) {
                    return true;
                }
                break;
            }
        }
    }
    return false;
}
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

foreach ($lecturers as $index => $lecturer) {
    $target = $BASE_TARGET + ($index < $REMAINDER ? 1 : 0);
    
    $lecturerAssignments[$lecturer->id] = [
        'lecturer_id' => $lecturer->id,
        'lecturer_name' => $lecturer->lecturer->user->name ?? 'Unknown',
        'student_ids' => [],
        'student_count' => 0,
        'target' => $target,
        'deficit' => $target,
        'groups' => [],
        'towns' => [], 
        'latitude' => null,  
        'longitude' => null, 
    ];
}
   
    $unassignedGroups = [];
    
   foreach ($studentGroups as $group) {
    $groupLat = $group['latitude'];
    $groupLng = $group['longitude'];
    $groupSize = $group['student_count'];
    $groupTown = $group['town'];
    
    
    $groupCluster = $this->findTownCluster($groupTown, $specialClusters);
    
    $eligibleLecturers = [];
    
    foreach ($lecturerAssignments as $lecturerId => $assignment) {
        
        if ($assignment['student_count'] + $groupSize > $MAX_STUDENTS_PER_LECTURER) {
            continue;
        }
        
        $canAssign = true;
        $clusterMatch = false;
        $clusterMaxDistance = $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS;

        if (!empty($assignment['towns'])) {
            foreach ($assignment['towns'] as $existingTown => $coords) {
                $distance = $this->haversineDistance(
                    $groupLat, $groupLng,
                    $coords['lat'], $coords['lng']
                );
                
               
                $existingCluster = $this->findTownCluster($existingTown, $specialClusters);
                
                if ($groupCluster && $existingCluster && $groupCluster === $existingCluster) {
                   
                    $clusterMaxDistance = $specialClusters[$groupCluster]['max_distance'];
                    
                   
                    $isNearAnyClusterTown = $this->isNearClusterTown(
                        $groupLat, $groupLng,
                        $specialClusters[$groupCluster]['towns'],
                        $towns,
                        $clusterMaxDistance
                    );
                    
                    if ($isNearAnyClusterTown) {
                        $clusterMatch = true;
                        continue; 
                    } else {
                        $canAssign = false;
                        break;
                    }
                } else {
                   
                    if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                        $canAssign = false;
                        break;
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
        
       
        $priority = ($deficit * 100) + (isset($assignment['towns'][$groupTown]) ? 50 : 0);
        
        if ($clusterMatch) {
           
            $priority += 150;
          
            $priority -= ($distanceToLecturer / 20);
        } else {
           
            $priority -= ($distanceToLecturer / 10);
        }
        
        $eligibleLecturers[] = [
            'lecturer_id' => $lecturerId,
            'distance_to_lecturer' => $distanceToLecturer,
            'current_load' => $assignment['student_count'],
            'deficit' => $deficit,
            'has_same_town' => isset($assignment['towns'][$groupTown]),
            'cluster_match' => $clusterMatch,
            'priority' => $priority
        ];
    }
    
    if (empty($eligibleLecturers)) {
        $unassignedGroups[] = $group;
        continue;
    }
    
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
    
    
    if ($lecturerAssignments[$bestLecturerId]['latitude'] === null) {
        $lecturerAssignments[$bestLecturerId]['latitude'] = $groupLat;
        $lecturerAssignments[$bestLecturerId]['longitude'] = $groupLng;
    } else {
        $lecturerAssignments[$bestLecturerId]['latitude'] = 
            ($lecturerAssignments[$bestLecturerId]['latitude'] + $groupLat) / 2;
        $lecturerAssignments[$bestLecturerId]['longitude'] = 
            ($lecturerAssignments[$bestLecturerId]['longitude'] + $groupLng) / 2;
    }
}
    if (!empty($unassignedGroups)) {
        \Log::info("Second pass: " . count($unassignedGroups) . " groups to assign");
        
        
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
        
       
        $lecturersByLoad = array_keys($lecturerAssignments);
        usort($lecturersByLoad, fn($a, $b) => 
            $lecturerAssignments[$a]['student_count'] <=> $lecturerAssignments[$b]['student_count']
        );
        
        
        foreach ($individualStudents as $student) {
            $town = $student['town'];
            $lat = $student['latitude'];
            $lng = $student['longitude'];
            
            $eligibleLecturers = [];
            
            foreach ($lecturersByLoad as $lecturerId) {
                $assignment = $lecturerAssignments[$lecturerId];
                
                
                if ($assignment['student_count'] + 1 > $MAX_STUDENTS_PER_LECTURER + 2) {
                    continue;
                }
                
               $canAssign = true;
$groupCluster = $this->findTownCluster($town, $specialClusters);

if (!empty($assignment['towns'])) {
    foreach ($assignment['towns'] as $existingTown => $coords) {
        $distance = $this->haversineDistance(
            $lat, $lng,
            $coords['lat'], $coords['lng']
        );
        
        
        $existingCluster = $this->findTownCluster($existingTown, $specialClusters);
        
        if ($groupCluster && $existingCluster && $groupCluster === $existingCluster) {
            
            $clusterMaxDistance = $specialClusters[$groupCluster]['max_distance'];
            
            $isNearAny = $this->isNearClusterTown(
                $lat, $lng,
                $specialClusters[$groupCluster]['towns'],
                $towns,
                $clusterMaxDistance
            );
            
            if (!$isNearAny) {
                $canAssign = false;
                break;
            }
        } else {
            
            if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                $canAssign = false;
                break;
            }
        }
    }
}

if (!$canAssign) {
    continue;
}
                
                $eligibleLecturers[] = $lecturerId;
            }
            
            if (!empty($eligibleLecturers)) {
                
                $bestLecturerId = $eligibleLecturers[0]; 
                
                $lecturerAssignments[$bestLecturerId]['student_ids'][] = $student['student_id'];
                $lecturerAssignments[$bestLecturerId]['student_count']++;
                if (!isset($lecturerAssignments[$bestLecturerId]['towns'][$town])) {
                    $lecturerAssignments[$bestLecturerId]['towns'][$town] = [
                        'lat' => $lat,
                        'lng' => $lng
                    ];
                }
                
                
                usort($lecturersByLoad, fn($a, $b) => 
                    $lecturerAssignments[$a]['student_count'] <=> $lecturerAssignments[$b]['student_count']
                );
            }
        }
    }
$allStudentIds = collect($towns)->flatMap(fn($t) => $t['student_ids'])->toArray();
$assignedIds = [];
foreach ($lecturerAssignments as $assignment) {
    if (isset($assignment['student_ids']) && is_array($assignment['student_ids'])) {
        $assignedIds = array_merge($assignedIds, $assignment['student_ids']);
    }
}

$missingIds = array_diff($allStudentIds, $assignedIds);

if (!empty($missingIds)) {
    \Log::warning("PASS 3: " . count($missingIds) . " students unassigned - forcing assignment");
    
    
    $lecturerIds = array_keys($lecturerAssignments);
    usort($lecturerIds, fn($a, $b) => 
        ($lecturerAssignments[$a]['student_count'] ?? 0) <=> ($lecturerAssignments[$b]['student_count'] ?? 0)
    );
    
    foreach ($missingIds as $studentId) {
       
        if (!isset($studentsById[$studentId])) {
            \Log::error("Student data not found for ID: {$studentId}");
            continue;
        }
        
        $studentData = $studentsById[$studentId];
        $town = $studentData->town_name ?? 'Unknown';
        $lat = $studentData->lat ?? 0;
        $lng = $studentData->lng ?? 0;
        
        $eligibleLecturers = [];
        
        foreach ($lecturerIds as $lecturerId) {
            if (!isset($lecturerAssignments[$lecturerId])) continue;
            
            $assignment = $lecturerAssignments[$lecturerId];
            
          
            $proximityPass = true;
            if (!empty($assignment['towns']) && is_array($assignment['towns'])) {
                foreach ($assignment['towns'] as $existingTown => $coords) {
                    
                    if (!isset($coords['lat']) || !isset($coords['lng'])) continue;
                    
                    $distance = $this->haversineDistance(
                        $lat, $lng,
                        $coords['lat'], $coords['lng']
                    );
                    
                    if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                        $proximityPass = false;
                        break;
                    }
                }
            }
            
            if (!$proximityPass) {
                continue; 
            }
            

            $eligibleLecturers[] = $lecturerId;
        }
        
        if (!empty($eligibleLecturers)) {
            
            usort($eligibleLecturers, fn($a, $b) => 
                ($lecturerAssignments[$a]['student_count'] ?? 0) <=> ($lecturerAssignments[$b]['student_count'] ?? 0)
            );
            
            $bestLecturerId = $eligibleLecturers[0];
            
            
            if (!isset($lecturerAssignments[$bestLecturerId]['student_ids'])) {
                $lecturerAssignments[$bestLecturerId]['student_ids'] = [];
            }
            if (!is_array($lecturerAssignments[$bestLecturerId]['student_ids'])) {
                $lecturerAssignments[$bestLecturerId]['student_ids'] = [];
            }
            
          
            $lecturerAssignments[$bestLecturerId]['student_ids'][] = $studentId;
            $lecturerAssignments[$bestLecturerId]['student_count'] = count($lecturerAssignments[$bestLecturerId]['student_ids']);
            
            if (!isset($lecturerAssignments[$bestLecturerId]['towns'][$town])) {
                if (!isset($lecturerAssignments[$bestLecturerId]['towns'])) {
                    $lecturerAssignments[$bestLecturerId]['towns'] = [];
                }
                $lecturerAssignments[$bestLecturerId]['towns'][$town] = [
                    'lat' => $lat,
                    'lng' => $lng
                ];
            }
            
           
            if (count($lecturerAssignments[$bestLecturerId]['towns'] ?? []) == 1) {
                $lecturerAssignments[$bestLecturerId]['latitude'] = $lat;
                $lecturerAssignments[$bestLecturerId]['longitude'] = $lng;
            }
        } else {
            
            \Log::error("CRITICAL: Student {$studentId} from {$town} has NO lecturers within {$MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS}km!");
            
            
            $bestLecturerId = $lecturerIds[0];
            if (!isset($lecturerAssignments[$bestLecturerId]['student_ids'])) {
                $lecturerAssignments[$bestLecturerId]['student_ids'] = [];
            }
            $lecturerAssignments[$bestLecturerId]['student_ids'][] = $studentId;
            $lecturerAssignments[$bestLecturerId]['student_count'] = count($lecturerAssignments[$bestLecturerId]['student_ids']);
        }
    }
}

$rebalancedCount = 0;
$balancePasses = 0;
$maxBalancePasses = 3;

while ($balancePasses < $maxBalancePasses) {
    $balancePasses++;
    $changesThisPass = 0;
    
   
    $emptyLecturers = [];
    $underloadedLecturers = [];
    $overloadedLecturers = [];
    $healthyLecturers = [];
    
    foreach ($lecturerAssignments as $lecturerId => $assignment) {
        $load = $assignment['student_count'] ?? 0;
        if ($load == 0) {
            $emptyLecturers[] = $lecturerId;
        } elseif ($load < $BASE_TARGET) {
            $underloadedLecturers[$lecturerId] = $load;
        } elseif ($load > $MAX_STUDENTS_PER_LECTURER) {
            $overloadedLecturers[$lecturerId] = $load - $MAX_STUDENTS_PER_LECTURER;
        } else {
            $healthyLecturers[] = $lecturerId;
        }
    }
    
   
    if (!empty($emptyLecturers) && (!empty($overloadedLecturers) || !empty($healthyLecturers))) {
        foreach ($emptyLecturers as $emptyId) {
            
            if (!isset($lecturerAssignments[$emptyId])) continue;
            
            $empty = &$lecturerAssignments[$emptyId];
            
            
            if (!isset($empty['student_ids']) || !is_array($empty['student_ids'])) {
                $empty['student_ids'] = [];
            }
            if (!isset($empty['towns']) || !is_array($empty['towns'])) {
                $empty['towns'] = [];
            }
            
            
            $donorFound = false;
            
            
            foreach ($overloadedLecturers as $donorId => $excess) {
                if ($excess <= 0) continue;
                if (!isset($lecturerAssignments[$donorId])) continue;
                
                $donor = &$lecturerAssignments[$donorId];
                
               
                if (empty($donor['student_ids']) || !is_array($donor['student_ids'])) continue;
                
                
                $movableStudents = [];
                foreach ($donor['student_ids'] as $sid) {
                   
                    if (!isset($studentsById[$sid])) continue;
                    
                    $studentData = $studentsById[$sid];
                    $studentLat = $studentData->lat ?? 0;
                    $studentLng = $studentData->lng ?? 0;
                    
                  
                    $proximityOk = true;
                    foreach ($empty['towns'] as $existingTown => $coords) {
                        if (!isset($coords['lat']) || !isset($coords['lng'])) continue;
                        
                        $distance = $this->haversineDistance(
                            $studentLat, $studentLng,
                            $coords['lat'], $coords['lng']
                        );
                        if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                            $proximityOk = false;
                            break;
                        }
                    }
                    
                    if ($proximityOk) {
                        $movableStudents[] = $sid;
                    }
                }
                
                
                $needed = $BASE_TARGET; 
                $takeCount = min(3, $excess, count($movableStudents), $needed);
                
                if ($takeCount > 0) {
                    $toMove = array_slice($movableStudents, 0, $takeCount);
                    
                    
                    $donor['student_ids'] = array_diff($donor['student_ids'], $toMove);
                    $donor['student_count'] = count($donor['student_ids']);
                    
                    $empty['student_ids'] = array_merge($empty['student_ids'], $toMove);
                    $empty['student_count'] = count($empty['student_ids']);
                    
                    
                    foreach ($toMove as $sid) {
                        if (!isset($studentsById[$sid])) continue;
                        
                        $studentData = $studentsById[$sid];
                        $studentTown = $studentData->town_name ?? 'Unknown';
                        $studentLat = $studentData->lat ?? 0;
                        $studentLng = $studentData->lng ?? 0;
                        
                        if (!isset($empty['towns'][$studentTown])) {
                            $empty['towns'][$studentTown] = [
                                'lat' => $studentLat,
                                'lng' => $studentLng
                            ];
                        }
                    }
                    
                 
                    if (count($empty['towns']) == 1 && !empty($toMove) && isset($studentsById[$toMove[0]])) {
                        $empty['latitude'] = $studentsById[$toMove[0]]->lat ?? 0;
                        $empty['longitude'] = $studentsById[$toMove[0]]->lng ?? 0;
                    }
                    
                    $overloadedLecturers[$donorId] -= $takeCount;
                    $changesThisPass += $takeCount;
                    $donorFound = true;
                    
                    \Log::info("REBALANCE: Moved {$takeCount} students to empty lecturer {$empty['lecturer_name']}");
                    break;
                }
            }
            
           
            if (!$donorFound && !empty($healthyLecturers)) {
                foreach ($healthyLecturers as $donorId) {
                    if (!isset($lecturerAssignments[$donorId])) continue;
                    
                    $donor = &$lecturerAssignments[$donorId];
                    
                    
                    if (empty($donor['student_ids']) || !is_array($donor['student_ids'])) continue;
                    
                    
                    if (($donor['student_count'] ?? 0) - 2 < $BASE_TARGET) continue;
                    
                   
                    $movableStudents = [];
                    foreach ($donor['student_ids'] as $sid) {
                        if (!isset($studentsById[$sid])) continue;
                        
                        $studentData = $studentsById[$sid];
                        $studentLat = $studentData->lat ?? 0;
                        $studentLng = $studentData->lng ?? 0;
                        
                        $proximityOk = true;
                        foreach ($empty['towns'] as $existingTown => $coords) {
                            if (!isset($coords['lat']) || !isset($coords['lng'])) continue;
                            
                            $distance = $this->haversineDistance(
                                $studentLat, $studentLng,
                                $coords['lat'], $coords['lng']
                            );
                            if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                                $proximityOk = false;
                                break;
                            }
                        }
                        
                        if ($proximityOk) {
                            $movableStudents[] = $sid;
                        }
                    }
                    
                    $takeCount = min(2, count($movableStudents));
                    if ($takeCount > 0) {
                        $toMove = array_slice($movableStudents, 0, $takeCount);
                        
                        $donor['student_ids'] = array_diff($donor['student_ids'], $toMove);
                        $donor['student_count'] = count($donor['student_ids']);
                        
                        $empty['student_ids'] = array_merge($empty['student_ids'], $toMove);
                        $empty['student_count'] = count($empty['student_ids']);
                        
                        foreach ($toMove as $sid) {
                            if (!isset($studentsById[$sid])) continue;
                            
                            $studentData = $studentsById[$sid];
                            $studentTown = $studentData->town_name ?? 'Unknown';
                            $studentLat = $studentData->lat ?? 0;
                            $studentLng = $studentData->lng ?? 0;
                            
                            if (!isset($empty['towns'][$studentTown])) {
                                $empty['towns'][$studentTown] = [
                                    'lat' => $studentLat,
                                    'lng' => $studentLng
                                ];
                            }
                        }
                        
                        if (count($empty['towns']) == 1 && !empty($toMove) && isset($studentsById[$toMove[0]])) {
                            $empty['latitude'] = $studentsById[$toMove[0]]->lat ?? 0;
                            $empty['longitude'] = $studentsById[$toMove[0]]->lng ?? 0;
                        }
                        
                        $changesThisPass += $takeCount;
                        \Log::info("REBALANCE: Moved {$takeCount} students from healthy to empty lecturer {$empty['lecturer_name']}");
                        break;
                    }
                }
            }
        }
    }
    
   
    $underloadedIds = array_keys($underloadedLecturers);
    $overloadedIds = array_keys($overloadedLecturers);
    
    if (!empty($underloadedIds) && !empty($overloadedIds)) {
        foreach ($underloadedIds as $underId) {
            if (!isset($lecturerAssignments[$underId])) continue;
            
            $under = &$lecturerAssignments[$underId];
            $needed = $BASE_TARGET - ($under['student_count'] ?? 0);
            if ($needed <= 0) continue;
            
            foreach ($overloadedIds as $overId) {
                if ($needed <= 0) break;
                if (!isset($lecturerAssignments[$overId])) continue;
                
                $over = &$lecturerAssignments[$overId];
                $excess = $overloadedLecturers[$overId] ?? 0;
                if ($excess <= 0) continue;
                
                
                if (empty($over['student_ids']) || !is_array($over['student_ids'])) continue;
                
               
                $movableStudents = [];
                foreach ($over['student_ids'] as $sid) {
                    if (!isset($studentsById[$sid])) continue;
                    
                    $studentData = $studentsById[$sid];
                    $studentLat = $studentData->lat ?? 0;
                    $studentLng = $studentData->lng ?? 0;
                    
                    
                    $proximityOk = true;
                    foreach (($under['towns'] ?? []) as $existingTown => $coords) {
                        if (!isset($coords['lat']) || !isset($coords['lng'])) continue;
                        
                        $distance = $this->haversineDistance(
                            $studentLat, $studentLng,
                            $coords['lat'], $coords['lng']
                        );
                        if ($distance > $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
                            $proximityOk = false;
                            break;
                        }
                    }
                    
                    if ($proximityOk) {
                        $movableStudents[] = $sid;
                    }
                }
                
                $moveCount = min(count($movableStudents), $needed, $excess);
                if ($moveCount > 0) {
                    $toMove = array_slice($movableStudents, 0, $moveCount);
                    
                    
                    $over['student_ids'] = array_diff($over['student_ids'], $toMove);
                    $over['student_count'] = count($over['student_ids']);
                    
                    $under['student_ids'] = array_merge($under['student_ids'], $toMove);
                    $under['student_count'] = count($under['student_ids']);
                    
                 
                    foreach ($toMove as $sid) {
                        if (!isset($studentsById[$sid])) continue;
                        
                        $studentData = $studentsById[$sid];
                        $studentTown = $studentData->town_name ?? 'Unknown';
                        $studentLat = $studentData->lat ?? 0;
                        $studentLng = $studentData->lng ?? 0;
                        
                        if (!isset($under['towns'][$studentTown])) {
                            if (!isset($under['towns'])) {
                                $under['towns'] = [];
                            }
                            $under['towns'][$studentTown] = [
                                'lat' => $studentLat,
                                'lng' => $studentLng
                            ];
                        }
                    }
                    
                    $needed -= $moveCount;
                    $overloadedLecturers[$overId] -= $moveCount;
                    $changesThisPass += $moveCount;
                    
                    \Log::info("REBALANCE: Moved {$moveCount} students to underloaded lecturer {$under['lecturer_name']}");
                }
            }
        }
    }
    
    $rebalancedCount += $changesThisPass;
    
    
    if ($changesThisPass == 0) {
        break;
    }
}

$finalStudentIds = [];
foreach ($lecturerAssignments as $assignment) {
    if (isset($assignment['student_ids']) && is_array($assignment['student_ids'])) {
        $finalStudentIds = array_merge($finalStudentIds, $assignment['student_ids']);
    }
}

$stillMissing = array_diff($allStudentIds, $finalStudentIds);
$emptyLecturersFinal = [];

foreach ($lecturerAssignments as $id => $ass) {
    if (($ass['student_count'] ?? 0) == 0) {
        $emptyLecturersFinal[] = $id;
    }
}

if (!empty($stillMissing) || !empty($emptyLecturersFinal)) {
    \Log::warning("FINAL EMERGENCY: " . count($stillMissing) . " students missing, " . count($emptyLecturersFinal) . " lecturers empty");
    
    $lecturerIds = array_keys($lecturerAssignments);
    usort($lecturerIds, fn($a, $b) => 
        ($lecturerAssignments[$a]['student_count'] ?? 0) <=> ($lecturerAssignments[$b]['student_count'] ?? 0)
    );
    
    foreach ($emptyLecturersFinal as $emptyId) {
        if (empty($stillMissing)) break;
        if (!isset($lecturerAssignments[$emptyId])) continue;
        
        $empty = &$lecturerAssignments[$emptyId];
        
        
        if (!isset($empty['student_ids']) || !is_array($empty['student_ids'])) {
            $empty['student_ids'] = [];
        }
        if (!isset($empty['towns']) || !is_array($empty['towns'])) {
            $empty['towns'] = [];
        }
        
        $takeCount = min(3, count($stillMissing));
        
        if ($takeCount > 0) {
            $toMove = array_slice($stillMissing, 0, $takeCount);
            $stillMissing = array_diff($stillMissing, $toMove);
            
            foreach ($toMove as $sid) {
                if (!isset($studentsById[$sid])) continue;
                
                $empty['student_ids'][] = $sid;
                
                $studentData = $studentsById[$sid];
                $town = $studentData->town_name ?? 'Unknown';
                $lat = $studentData->lat ?? 0;
                $lng = $studentData->lng ?? 0;
                
                if (!isset($empty['towns'][$town])) {
                    $empty['towns'][$town] = [
                        'lat' => $lat,
                        'lng' => $lng
                    ];
                }
            }
            
            $empty['student_count'] = count($empty['student_ids']);
            
            if (count($empty['towns']) == 1 && !empty($toMove) && isset($studentsById[$toMove[0]])) {
                $empty['latitude'] = $studentsById[$toMove[0]]->lat ?? 0;
                $empty['longitude'] = $studentsById[$toMove[0]]->lng ?? 0;
            }
            
            \Log::info("EMERGENCY: Assigned {$takeCount} students to empty lecturer {$empty['lecturer_name']}");
        }
    }
    
 
    if (!empty($stillMissing)) {
        usort($lecturerIds, fn($a, $b) => 
            ($lecturerAssignments[$a]['student_count'] ?? 0) <=> ($lecturerAssignments[$b]['student_count'] ?? 0)
        );
        
        foreach ($stillMissing as $sid) {
            if (!isset($studentsById[$sid])) continue;
            
            $bestId = $lecturerIds[0];
            
            if (!isset($lecturerAssignments[$bestId]['student_ids']) || !is_array($lecturerAssignments[$bestId]['student_ids'])) {
                $lecturerAssignments[$bestId]['student_ids'] = [];
            }
            
            $lecturerAssignments[$bestId]['student_ids'][] = $sid;
            $lecturerAssignments[$bestId]['student_count'] = count($lecturerAssignments[$bestId]['student_ids']);
            
            usort($lecturerIds, fn($a, $b) => 
                ($lecturerAssignments[$a]['student_count'] ?? 0) <=> ($lecturerAssignments[$b]['student_count'] ?? 0)
            );
        }
    }
}


$totalLecturersWithStudents = 0;
foreach ($lecturerAssignments as $assignment) {
    if (($assignment['student_count'] ?? 0) > 0) {
        $totalLecturersWithStudents++;
    }
}

\Log::info("REBALANCING COMPLETE: Moved {$rebalancedCount} students, {$totalLecturersWithStudents}/" . count($lecturerAssignments) . " lecturers have students");
    
$rebalanced = 0;

$allTowns = [];
$townCoordinates = [];


foreach ($studentsById as $sid => $student) {
    $townName = $student->town_name;
    if (!isset($allTowns[$townName])) {
        $allTowns[$townName] = [
            'lat' => $student->lat,
            'lng' => $student->lng,
            'students' => 0
        ];
    }
    $allTowns[$townName]['students']++;
}


foreach ($lecturerAssignments as $lid => $ass) {
    foreach ($ass['towns'] as $town => $coords) {
        if (!isset($allTowns[$town])) {
            $allTowns[$town] = [
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'students' => 0
            ];
        }
    }
}

$townClusters = [];
$processedTowns = [];

foreach ($allTowns as $town1 => $data1) {
    if (in_array($town1, $processedTowns)) continue;
    
    $cluster = [
        'name' => $town1,
        'towns' => [$town1],
        'lat' => $data1['lat'],
        'lng' => $data1['lng'],
        'students' => $data1['students'],
        'lecturers' => [],
        'lecturer_ids' => []
    ];
    $processedTowns[] = $town1;
    
    foreach ($allTowns as $town2 => $data2) {
        if ($town1 == $town2) continue;
        if (in_array($town2, $processedTowns)) continue;
        
        $distance = $this->haversineDistance(
            $data1['lat'], $data1['lng'],
            $data2['lat'], $data2['lng']
        );
        
        if ($distance <= $MAX_DISTANCE_BETWEEN_DIFFERENT_TOWNS) {
            $cluster['towns'][] = $town2;
            $cluster['students'] += $data2['students'];
            $processedTowns[] = $town2;
            
            
            $cluster['lat'] = ($cluster['lat'] + $data2['lat']) / 2;
            $cluster['lng'] = ($cluster['lng'] + $data2['lng']) / 2;
        }
    }
    
    $townClusters[] = $cluster;
}

foreach ($townClusters as &$cluster) {
    foreach ($lecturerAssignments as $lid => $ass) {
        foreach ($ass['towns'] as $town => $coords) {
            if (in_array($town, $cluster['towns']) && !in_array($lid, $cluster['lecturer_ids'])) {
                $cluster['lecturer_ids'][] = $lid;
               
                $studentCount = 0;
                foreach ($ass['student_ids'] as $sid) {
                    if (isset($studentsById[$sid]) && in_array($studentsById[$sid]->town_name, $cluster['towns'])) {
                        $studentCount++;
                    }
                }
                $cluster['lecturers'][] = [
                    'id' => $lid,
                    'students' => $studentCount,
                    'name' => $ass['lecturer_name']
                ];
            }
        }
    }
}

$totalStudents = array_sum(array_column($townClusters, 'students'));
$totalLecturers = count($lecturerAssignments);

\Log::info("=" . str_repeat("=", 80));
\Log::info("DYNAMIC CLUSTER ANALYSIS");
\Log::info("=" . str_repeat("=", 80));

foreach ($townClusters as $index => &$cluster) {
    $studentCount = $cluster['students'];
    $lecturerCount = count($cluster['lecturer_ids']);
    $currentAvg = $lecturerCount > 0 ? round($studentCount / $lecturerCount, 1) : 0;
    
   
    $idealLecturers = max(1, round(($studentCount / $totalStudents) * $totalLecturers));
    
    
    if ($studentCount >= 50) {
        $priority = "🔴 HIGH";
        
        $targetLecturers = min($idealLecturers + 1, $totalLecturers);
    } elseif ($studentCount >= 20) {
        $priority = "🟡 MEDIUM";
        $targetLecturers = $idealLecturers;
    } else {
        $priority = "🟢 LOW";
        $targetLecturers = max(1, $idealLecturers - 1);
    }
    
    $cluster['priority'] = $priority;
    $cluster['student_count'] = $studentCount;
    $cluster['lecturer_count'] = $lecturerCount;
    $cluster['current_avg'] = $currentAvg;
    $cluster['ideal_lecturers'] = $idealLecturers;
    $cluster['target_lecturers'] = $targetLecturers;
    $cluster['needs_lecturers'] = $targetLecturers - $lecturerCount;
    
    \Log::info("{$priority} PRIORITY: Cluster {$index} - {$cluster['name']}");
    \Log::info("  Towns: " . implode(', ', array_slice($cluster['towns'], 0, 3)) . (count($cluster['towns']) > 3 ? '...' : ''));
    \Log::info("  Students: {$studentCount}, Lecturers: {$lecturerCount}, Avg: {$currentAvg}");
    \Log::info("  Ideal: {$idealLecturers} lecturers, Target: {$targetLecturers} lecturers");
    \Log::info("  " . ($cluster['needs_lecturers'] > 0 ? "NEEDS {$cluster['needs_lecturers']} MORE" : ($cluster['needs_lecturers'] < 0 ? "Has " . abs($cluster['needs_lecturers']) . " extra" : "Perfect")));
}


$highPriorityClusters = array_filter($townClusters, fn($c) => $c['priority'] === "🔴 HIGH" && $c['needs_lecturers'] > 0);
$lowPriorityClusters = array_filter($townClusters, fn($c) => $c['priority'] !== "🔴 HIGH" && $c['needs_lecturers'] < 0);

if (!empty($highPriorityClusters) && !empty($lowPriorityClusters)) {
    \Log::info("=" . str_repeat("=", 80));
    \Log::info("REDISTRIBUTING LECTURERS - Large groups get priority");
    \Log::info("=" . str_repeat("=", 80));
    
    foreach ($highPriorityClusters as $highIndex => $highCluster) {
        $needed = $highCluster['needs_lecturers'];
        if ($needed <= 0) continue;
        
        foreach ($lowPriorityClusters as $lowIndex => $lowCluster) {
            if ($needed <= 0) break;
            
            $available = abs($lowCluster['needs_lecturers']);
            if ($available <= 0) continue;
            
            $take = min($needed, $available);
            \Log::info("Moving {$take} lecturers from Cluster {$lowIndex} to Cluster {$highIndex}");
            
            
            $lecturersToMove = array_slice($lowCluster['lecturer_ids'], 0, $take);
            
            foreach ($lecturersToMove as $lid) {
                
                $studentsToTake = [];
                foreach ($lecturerAssignments[$lid]['student_ids'] as $sid) {
                    if (isset($studentsById[$sid]) && in_array($studentsById[$sid]->town_name, $highCluster['towns'])) {
                        $studentsToTake[] = $sid;
                    }
                }
                
                \Log::info("  Lecturer {$lid} has " . count($studentsToTake) . " students in target cluster");
            }
            
            $needed -= $take;
            $highCluster['needs_lecturers'] = $needed;
            $lowCluster['needs_lecturers'] += $take;
        }
    }
}


foreach ($townClusters as $clusterIndex => $cluster) {
    if (count($cluster['lecturer_ids']) <= 1) continue;
    
    \Log::info("=" . str_repeat("=", 80));
    \Log::info("BALANCING CLUSTER {$clusterIndex}: {$cluster['name']}");
    \Log::info("=" . str_repeat("=", 80));
    
  
    $clusterCounts = [];
    $clusterStudentIds = [];
    $totalInCluster = 0;
    
    foreach ($cluster['lecturer_ids'] as $lid) {
        $count = 0;
        $studentIds = [];
        foreach ($lecturerAssignments[$lid]['student_ids'] as $sid) {
            if (isset($studentsById[$sid]) && in_array($studentsById[$sid]->town_name, $cluster['towns'])) {
                $count++;
                $studentIds[] = $sid;
            }
        }
        $clusterCounts[$lid] = $count;
        $clusterStudentIds[$lid] = $studentIds;
        $totalInCluster += $count;
    }
    
    \Log::info("Total students: {$totalInCluster}");
    \Log::info("Current distribution: " . json_encode($clusterCounts));
    
 
    $numLecturers = count($cluster['lecturer_ids']);
    $studentsPerLecturer = floor($totalInCluster / $numLecturers);
    $remainder = $totalInCluster % $numLecturers;
    
    $targets = [];
    foreach ($cluster['lecturer_ids'] as $index => $lid) {
        $targets[$lid] = $studentsPerLecturer + ($index < $remainder ? 1 : 0);
    }
    
    \Log::info("Target distribution: " . json_encode($targets));
    
    
    $givers = [];
    $takers = [];
    
    foreach ($cluster['lecturer_ids'] as $lid) {
        $current = $clusterCounts[$lid];
        $target = $targets[$lid];
        
        if ($current > $target) {
            $givers[$lid] = [
                'excess' => $current - $target,
                'students' => $clusterStudentIds[$lid]
            ];
        } elseif ($current < $target) {
            $takers[$lid] = [
                'need' => $target - $current
            ];
        }
    }
    
   
    $clusterMoved = 0;
    
    foreach ($givers as $giverId => $giverData) {
        if (empty($takers)) break;
        
        $giverStudents = $giverData['students'];
        $excess = $giverData['excess'];
        
        foreach ($takers as $takerId => $takerData) {
            if ($excess <= 0) break;
            
            $need = $takerData['need'];
            $moveCount = min($excess, $need);
            
            if ($moveCount <= 0) continue;
            
            $toMove = array_slice($giverStudents, 0, $moveCount);
            
            if (!empty($toMove)) {
                
                $lecturerAssignments[$giverId]['student_ids'] = array_diff(
                    $lecturerAssignments[$giverId]['student_ids'],
                    $toMove
                );
                
               
                $lecturerAssignments[$takerId]['student_ids'] = array_merge(
                    $lecturerAssignments[$takerId]['student_ids'],
                    $toMove
                );
                
               
                $firstTown = $studentsById[$toMove[0]]->town_name;
                $firstLat = $studentsById[$toMove[0]]->lat;
                $firstLng = $studentsById[$toMove[0]]->lng;
                
               
                if (!isset($lecturerAssignments[$takerId]['towns'][$firstTown])) {
                    $lecturerAssignments[$takerId]['towns'][$firstTown] = [
                        'lat' => $firstLat,
                        'lng' => $firstLng
                    ];
                }
                
              
                $excess -= $moveCount;
                $giverStudents = array_slice($giverStudents, $moveCount);
                
                $takers[$takerId]['need'] -= $moveCount;
                if ($takers[$takerId]['need'] <= 0) {
                    unset($takers[$takerId]);
                }
                
                $clusterMoved += $moveCount;
                $rebalanced += $moveCount;
                
                \Log::info("  Moved {$moveCount} students from {$giverId} to {$takerId}");
            }
        }
    }
    
 
    foreach ($cluster['lecturer_ids'] as $lid) {
        $lecturerAssignments[$lid]['student_count'] = count($lecturerAssignments[$lid]['student_ids']);
    }
    
    
    $newCounts = [];
    foreach ($cluster['lecturer_ids'] as $lid) {
        $count = 0;
        foreach ($lecturerAssignments[$lid]['student_ids'] as $sid) {
            if (isset($studentsById[$sid]) && in_array($studentsById[$sid]->town_name, $cluster['towns'])) {
                $count++;
            }
        }
        $newCounts[$lid] = $count;
    }
    
    \Log::info("New distribution: " . json_encode($newCounts));
}

\Log::info("=" . str_repeat("=", 80));
\Log::info("✅ DYNAMIC PRIORITIZATION COMPLETE");
\Log::info("=" . str_repeat("=", 80));

foreach ($townClusters as $index => $cluster) {
    $finalCounts = [];
    foreach ($cluster['lecturer_ids'] as $lid) {
        $count = 0;
        foreach ($lecturerAssignments[$lid]['student_ids'] as $sid) {
            if (isset($studentsById[$sid]) && in_array($studentsById[$sid]->town_name, $cluster['towns'])) {
                $count++;
            }
        }
        $finalCounts[$lid] = $count;
    }
    
    $total = array_sum($finalCounts);
    $avg = count($finalCounts) > 0 ? round($total / count($finalCounts), 1) : 0;
    
    \Log::info("Cluster {$index} ({$cluster['name']}): {$total} students, " . count($finalCounts) . " lecturers, avg {$avg}");
    \Log::info("  Distribution: " . json_encode($finalCounts));
}

\Log::info("Total students moved: {$rebalanced}");
    
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
$assignedStudents = AttachmentStudent::with([
        'attachment',
        'student.user',
        'student.program.parent',
        'attachmentLecturer.lecturer.user',
        'company.town'
    ])
    ->where('attachment_id', $request->attachment_id)
    ->whereNotNull('attachment_lecturer_id')
    ->whereHas('student.program.parent', fn($q) => $q->where('id', $request->department_id))
    ->get();


$groupedData = [];
$counter = 1;


$lecturerGroups = [];
foreach ($assignedStudents as $student) {
   
    $lecturerName = 'Unknown';
    if ($student->attachmentLecturer && 
        $student->attachmentLecturer->lecturer && 
        $student->attachmentLecturer->lecturer->user) {
        $lecturerName = $student->attachmentLecturer->lecturer->user->name;
    }
    
    if (!isset($lecturerGroups[$lecturerName])) {
        $lecturerGroups[$lecturerName] = [];
    }
    $lecturerGroups[$lecturerName][] = $student;
}


ksort($lecturerGroups);


foreach ($lecturerGroups as $lecturerName => $students) {
    
    usort($students, function($a, $b) {
        return strcmp($a->student->user->name ?? '', $b->student->user->name ?? '');
    });
    
    foreach ($students as $student) {
        $groupedData[] = [
            'DT_RowIndex' => $counter++,
            'attachment' => $student->attachment->name ?? '-',
            'name' => $student->student->user->name ?? '-',
            'reg_no' => $student->student->reg_no ?? '-',
            'department' => $student->student->program->parent->name ?? '-',
            'lecturer' => $lecturerName,
            'company' => $student->company->name ?? '-',
            'town' => $student->company->town->name ?? '-',
            'phone_number' => $student->student->phone_number ?? '-'
        ];
    }
}

return response()->json([
    'draw' => 1, 
    'recordsTotal' => count($groupedData),
    'recordsFiltered' => count($groupedData),
    'data' => $groupedData,
    'status' => 'success',
    'message' => '✅ All students assigned successfully'
]);
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
private function findTownCluster($townName, $clusters) {
    foreach ($clusters as $clusterKey => $cluster) {
        if (in_array($townName, $cluster['towns'])) {
            return $clusterKey;
        }
    }
    return null;
}

private function isNearClusterTown($lat, $lng, $clusterTowns, $townsData, $maxDistance) {
    foreach ($clusterTowns as $clusterTown) {
        foreach ($townsData as $town) {
            if ($town['town_name'] === $clusterTown) {
                $distance = $this->haversineDistance(
                    $lat, $lng,
                    $town['latitude'], $town['longitude']
                );
                if ($distance <= $maxDistance) {
                    return true;
                }
                break;
            }
        }
    }
    return false;
}
}