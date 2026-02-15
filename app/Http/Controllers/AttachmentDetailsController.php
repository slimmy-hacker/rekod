<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\User;
use App\Models\Attachment;
use App\Models\AttachmentStudent;
use App\Models\Company;
use App\Models\Location;
use App\Models\DailyReport;
use App\Models\Student;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class AttachmentDetailsController extends Controller
{public function edit(Request $req)
{   
    $attachment_student_id = $req->session()->get('attachment_student_id');
$attachment_student = AttachmentStudent::find($attachment_student_id);

$attachment_student_id = $req->session()->get('attachment_student_id');
$attachment_student = AttachmentStudent::find($attachment_student_id);

$companies = Company::with('town')->get();


$logged_user = auth()->user();
$my_student_details = Student::with('program')
                            ->where('user_id', $logged_user->id)
                            ->first();

$counties = Location::where('level', 1)->get();


$towns = Location::where('level', 3)->get();

if ($attachment_student->company_id) {
    return view('student.form-submitted', compact('attachment_student_id'));
}


return view('student.attachment-form', compact(
    'companies', 'logged_user', 'my_student_details', 'attachment_student','attachment_student_id', 'counties', 'towns'
));



$logged_user = auth()->user();
$my_student_details = Student::with('program')
                            ->where('user_id', $logged_user->id)
                            ->first();

$counties = Location::where('level', 1)->orderBy('name', 'asc')->get();
$towns = Location::where('level', 3)->orderBy('name', 'asc')->get();

if ($attachment_student->company_id) {
    return view('student.form-submitted', compact('attachment_student_id'));
}


return view('student.attachment-form', compact(
    'companies', 'logged_user', 'my_student_details', 'attachment_student', 'counties', 'towns'
));
}

    public function update(Request $request)
{
    $attachment_id = $request->session()->get('attachment_id');
    $attachment = Attachment::findOrFail($attachment_id);

    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email', 
        'student_phone' => 'required', 
        'supervisor_email' => 'required|email',
        'supervisor_phone' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    
    auth()->user()->update([
        'phone_number' => $request->student_phone
    ]);

    
    $companyUser = User::updateOrCreate(
        ['email' => $request->email],
        [
            'name' => $request->name,
            'role' => 'company', 
            'password' => bcrypt('company123'), 
        ]
    );

   
    $company = Company::updateOrCreate(
        ['email' => $request->email],
        [
            'name' => $request->name,
            'alias' => $request->alias,
            'contact' => $request->contact,
            'address' => $request->address,
            'county_id' => $request->county_id,
            'town_id' => $request->town_id,
            'user_id' => $companyUser->id, 
            'street' => $request->street ?? 'N/A',
            'building' => $request->building ?? 'N/A',
        ]
    );

    
    $supervisorUser = \App\Models\User::updateOrCreate(
        ['email' => $request->supervisor_email],
        [
            'name' => $request->supervisor_name,
            'phone_number' => $request->supervisor_phone,
            'role' => 'industrial_supervisor', 
            'password' => bcrypt('password123'),
        ]
    );

   
    $supervisorProfile = \App\Models\IndustrialSupervisor::updateOrCreate(
        ['user_id' => $supervisorUser->id],
        [
            'company_id' => $company->id,
            'position_title' => 'Industrial Supervisor',
        ]
    );

   $attachment_student_id = $request->session()->get('attachment_student_id');

if (!$attachment_student_id) {
    return redirect()->back()->with('error', 'Session expired. Please refresh the page.');
}
    \App\Models\AttachmentStudent::where('id', $attachment_student_id)
        ->update([
            'company_id' => $company->id,
            'industrial_supervisor_id' => $supervisorProfile->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

    return redirect()->back()->with([
        'notification' => [
            'icon' => 'success',
            'title' => 'Success',
            'message' => 'Accounts created and details updated!'
        ]
    ]);
}

    public function show($id)
    {
        $data = AttachmentStudent::with([
            'student',
            'student.user',
            'student.program',
            'company.town',
            'industrialSupervisor.user',
            'lecturer',
            'attachment'
        ])->findOrFail($id);

        return response()->json($data);
    }
 public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx,xls'
    ]);

    try {

        // 1️⃣ Find attachment period
        $attachment = Attachment::where('name', 'LIKE', '%September%')
            ->where('name', 'LIKE', '%2024%')
            ->first();

        if (!$attachment) {
            return response()->json([
                'message' => 'Attachment period not found.'
            ], 422);
        }

        // 2️⃣ Read Excel
        $dataRows = Excel::toCollection(
            new class implements WithHeadingRow {
                public function headingRow(): int { return 1; }
            },
            $request->file('file')
        )->first();

        $processedCount = 0;

         foreach ($dataRows as $row) {

            $regNo = trim(data_get($row, 'reg_no'));
            if (!$regNo) continue;

            $student = Student::where('reg_no', $regNo)->first();
            if (!$student) continue;

            // ✅ Update student's phone number if provided
            $studentPhone = trim(data_get($row, 'student_phone'));
            if ($studentPhone) {
                $student->update(['phone_number' => $studentPhone]);
            }

            // ===============================
            // 3️⃣ COMPANY USER
            // ===============================

            $companyName = trim(data_get($row, 'name_of_organization', 'Unknown'));
            $companyEmail = data_get($row, 'email');

            if (empty($companyEmail)) {
                $companyEmail = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $companyName)) . '@company.com';
            }

            $companyUser = User::updateOrCreate(
                ['email' => $companyEmail],
                [
                    'name' => $companyName,
                    'role' => 'company',
                    'password' => bcrypt('company123'),
                ]
            );

            // ===============================
            // 4️⃣ LOCATION
            // ===============================

            $townName = trim(data_get($row, 'town'));
            $town = null;

            if ($townName) {
                $town = Location::where('level', 3)
                    ->where('name', 'LIKE', '%' . $townName . '%')
                    ->first();
            }

            $countyId = ($town && $town->parent_id)
                ? $town->parent_id
                : optional(Location::where('level', 1)->first())->id ?? 1;

            // ===============================
            // 5️⃣ COMPANY RECORD
            // ===============================

            $company = Company::updateOrCreate(
                ['email' => $companyEmail],
                [
                    'name'      => $companyName,
                    'user_id'   => $companyUser->id,
                    'alias'     => strtoupper(substr($companyName, 0, 3)) . '-' . $companyUser->id,
                    'contact'   => trim(data_get($row, 'contact')) ?: '07' . str_pad($companyUser->id, 8, '0', STR_PAD_LEFT),
                    'address'   => data_get($row, 'address', $townName . ' Road'),
                    'county_id' => $countyId,
                    'town_id'   => $town->id ?? null,
                    'street'    => data_get($row, 'street', 'N/A'),
                    'building'  => data_get($row, 'building', 'N/A'),
                ]
            );

            // ===============================
            // 6️⃣ SUPERVISOR CREATION
            // ===============================

            $supervisorEmail = trim(data_get($row, 'supervisor_email'));
            $supervisorName  = trim(data_get($row, 'name_of_indusrty_supervisor'));
            $supervisorPhone = trim(data_get($row, 'telephone_number_of_supervisor'));

            $industrialSupervisorId = null;

            if ($supervisorEmail) {

                $supervisorUser = User::updateOrCreate(
                    ['email' => $supervisorEmail],
                    [
                        'name' => $supervisorName ?: 'Industrial Supervisor',
                        'phone_number' => $supervisorPhone,
                        'role' => 'industrial_supervisor',
                        'password' => bcrypt('password123'),
                    ]
                );

                $supervisorProfile = \App\Models\IndustrialSupervisor::updateOrCreate(
                    ['user_id' => $supervisorUser->id],
                    [
                        'company_id' => $company->id,
                        'position_title' => 'Industrial Supervisor',
                    ]
                );

                $industrialSupervisorId = $supervisorProfile->id;
            }

            // ===============================
            // 7️⃣ ATTACHMENT STUDENT UPDATE
            // ===============================

            AttachmentStudent::updateOrCreate(
                [
                    'student_id'    => $student->id,
                    'attachment_id' => $attachment->id,
                ],
                [
                    'company_id' => $company->id,
                    'industrial_supervisor_id' => $industrialSupervisorId,
                    'start_date' => $this->transformDate(data_get($row, 'date_started')),
                    'end_date'   => $this->transformDate(data_get($row, 'expected_date_of_completion')),
                ]
            );

            $processedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Successfully imported {$processedCount} student records."
        ]);

    } catch (\Exception $e) {

        \Log::error('Import Error: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

private function transformDate($value)
{
    if (empty($value)) return null;

    try {

        if (is_numeric($value)) {
            return \Carbon\Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
            )->format('Y-m-d');
        }

        return \Carbon\Carbon::parse($value)->format('Y-m-d');

    } catch (\Exception $e) {
        return null;
    }
}


    public function showImportPage()
{
    // Make sure this file exists in: resources/views/admin/attachments/import.blade.php
    return view('admin.attachmentimport');
}
}
