<?php
namespace App\Imports;

use App\Models\Attachment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class AttachmentImport implements ToModel, WithHeadingRow
{
   
       public function model(array $row)
{
    // 1. Find the student in the database using the Reg No from your CSV
    $student = Student::where('reg_no', $row['reg_no'])->first();

    if ($student) {
        // 2. Create or Update the attachment form for THAT specific student
        return Attachment::updateOrCreate(
            ['student_id' => $student->id], // Look for this student's form
            [
                'name'       => $row['name_of_organization'],
                'start_date' => Carbon::createFromFormat('d/m/Y', $row['date_started']),
                'end_date'   => Carbon::createFromFormat('d/m/Y', $row['expected_date_finish']),
                'town'       => $row['town'],
                'street'     => $row['street'] ?? null,
                'building'   => $row['building'] ?? null,
            ]
        );
    }
}
}