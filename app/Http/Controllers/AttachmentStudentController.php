<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\AttachmentStudentsImport;
use App\Models\Attachment;
use App\Models\AttachmentStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AttachmentStudentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = AttachmentStudent::with([
                'attachment',
                'student',
                'student.user'
            ]);

            if (!empty($request->attachment_id)) {
                $data->where('attachment_id', $request->attachment_id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->student && $row->student->user
                        ? $row->student->user->name
                        : '-';
                })
                ->addColumn('reg_no', fn($row) => $row->student->reg_no ?? '-')
                ->addColumn('attachment', fn($row) => $row->attachment->name ?? '-')
                ->addColumn('department', fn($row) => $row->department->name ?? '-')
                ->addColumn('lecturer', fn($row) => $row->lecturer->user->name ?? '-')
                ->addColumn('status', fn($row) => $row->attachment->status ?? '-')
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)"
                           data-id="' . $row->id . '"
                           class="w-auto text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium inline-flex items-center justify-center rounded-lg text-xs px-2 py-1 text-center open-student_attachment_details_modal-btn">
                            Profile
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $attachments = Attachment::select('id', 'name')
            ->orderBy('start_date', 'desc')
            ->get();

        $students = Student::select('id', 'user_id')
            ->with('user:id,name')
            ->get();

        return view('admin.attachment_students', compact('attachments', 'students'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new AttachmentStudentsImport();

            Excel::import($import, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'File processed',
                'stats' => [
                    'success_count' => $import->successCount,
                    'fail_count' => count($import->failedRecords),
                    'failed_records' => $import->failedRecords,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error uploading file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'attachment_id' => 'required|exists:attachments,id',
        ]);

        $exists = AttachmentStudent::where('student_id', $validated['student_id'])
            ->where('attachment_id', $validated['attachment_id'])
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment student already exists',
            ]);
        }

        $attachmentStudent = AttachmentStudent::create([
            'student_id' => $validated['student_id'],
            'attachment_id' => $validated['attachment_id'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Attachment student created successfully',
            'data' => $attachmentStudent,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reg_no' => 'required|string|max:255',
            'attachment_id' => 'required|exists:attachments,id',
            'department_id' => 'nullable|integer',
            'industrial_supervisor_id' => 'nullable|integer',
            'status' => 'required|string|max:255',
        ]);

        $student = Student::where('reg_no', $validated['reg_no'])->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found.',
            ], 404);
        }

        $attachmentStudent = AttachmentStudent::create([
            'student_id' => $student->id,
            'attachment_id' => $validated['attachment_id'],
            'department_id' => $validated['department_id'] ?? null,
            'industrial_supervisor_id' => $validated['industrial_supervisor_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Attachment student record created successfully.',
            'data' => $attachmentStudent,
        ], 201);
    }
}