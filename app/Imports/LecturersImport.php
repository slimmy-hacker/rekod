<?php
namespace App\Imports;

use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Validator;

class LecturersImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
use Importable, SkipsFailures;

public function model(array $row)
{
// Validate the row
$validator = Validator::make($row, [
'name' => 'required|string',
'email' => 'required|email|unique:users,email',
'phone_number' => 'nullable|string',
'staff_number' => 'required|string',
'department' => 'required|string',
'office_location' => 'nullable|string',
]);

if ($validator->fails()) {
// Skip this row, errors are collected in SkipsFailures
$this->onFailure($validator->errors()->all());
return null;
}

// Use DB transaction to ensure consistency
return DB::transaction(function () use ($row) {
$staff_no = Str::upper($row['staff_number']);

$user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                'name' => $row['name'],
                'phone_number' => $row['phone_number'],
                'password' => Hash::make($staff_no),
                    'role' => 'school_supervisor',
                ]
            );

        Lecturer::updateOrCreate(
            ['user_id' => $user->id],
            [
            'staff_number' => $staff_no,
            'department' => $row['department'],
            'office_location' => $row['office_location'],
            'office_phone' => $row['phone_number'],
        ]
        );

return $user;
});
}
}
