<?php
namespace App\Imports;
use App\Models\Attachment;
use App\Models\AttachmentStudent;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class AttachmentStudentsImport implements ToModel, WithHeadingRow, SkipsOnFailure, WithValidation

{
use Importable, SkipsFailures;

    public $successCount = 0;
    public $failedRecords = [];

    public function rules(): array
    {
        return [
            '*.reg_no'            => ['required'],
            '*.attachment_slug'   => ['required'],
        ];
    }



    public function model(array $row)
    {
        try {
            $reg  = strtoupper(trim($row['reg_no'] ?? ''));
            $slug = strtolower(trim($row['attachment_slug'] ?? ''));
            $student = Student::where('reg_no', $reg)->first();
            $attachment = Attachment::where('slug', $slug)->first();

            $errors = [];

            if (!$student) {
                $errors[] = "Student with reg_no '{$row['reg_no']}' not found";
            }

            if (!$attachment) {
                $errors[] = "Attachment with slug '{$row['attachment_slug']}' not found";
            }
            if($student && $attachment) {
                $attachment_student = AttachmentStudent::where('student_id', $student->id)
                    ->where('attachment_id', $attachment->id)
                    ->first();
                if ($attachment_student) {
                    $errors[] = "Student with reg_no '{$row['reg_no']}' for attachment '{$row['attachment_slug']}' had already been uploaded";
                }
            }

            if (!empty($errors)) {
                $this->failedRecords[] = [
                    'row' => $row,
                    'reason' => implode(" | ", $errors)
                ];
                return null;
            }


            
                AttachmentStudent::create([
            'student_id'            => $student->id,
            'attachment_id'         => $attachment->id,
            'company_name'          => $row['company_name'] ?? null,
            'start_date'            => isset($row['start_date']) ? \Carbon\Carbon::parse($row['start_date'])->format('Y-m-d') : null,
            'end_date'              => isset($row['end_date']) ? \Carbon\Carbon::parse($row['end_date'])->format('Y-m-d') : null,
            'town'                  => $row['town'] ?? null,
            'street'                => $row['street'] ?? null,
            'building'              => $row['building'] ?? null,
            'industrial_supervisor' => $row['industrial_supervisor'] ?? null,
            'program'               => $row['programme'] ?? null, // Note: your excel says "programme"
        ]);
            

            $this->successCount++;
        } catch (\Exception $e) {
            $this->failedRecords[] = [
                'row' => $row,
                'reason' => $e->getMessage()
            ];
            return null;
        }


    }

}
