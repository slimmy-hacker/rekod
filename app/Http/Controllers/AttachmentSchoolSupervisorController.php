<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttachmentSchoolSupervisor;
use App\Models\AttachmentSchedule;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AttachmentSchoolSupervisorController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AttachmentSchoolSupervisor::with(['schedule', 'department'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn() // adds DT_RowIndex
                ->addColumn('name', fn ($row) => $row->schedule->slug ?? '-')
                ->addColumn('attachment', fn ($row) => $row->schedule->slug ?? '-')
                ->addColumn('department', fn ($row) => $row->department->slug ?? '-')
                ->addColumn('students', fn ($row) => $row->department->slug ?? 0)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.attarchment_schedule_supervisors');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        $file = $request->file('file');

        // Read file rows
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($rows); // remove header row

        $errors = [];
        $inserted = 0;

        foreach ($rows as $index => $row) {
            $rowData = array_combine($header, $row);

          /*  $validator = Validator::make($rowData, [
                'staff_no' => 'required|string',
                'attachment_slug' => 'required|exists:attachment_schedules,slug',
                'department_slug' => 'required|exists:departments,slug',
            ]);*/
            $validator = Validator::make($rowData, [
                'staff_no' => 'required|string',
                'attachment_slug' => 'required',
                'department_slug' => 'required',
            ]);

            if ($validator->fails()) {
                $errors[$index + 2] = $validator->errors()->all(); // +2 because of header
                continue;
            }


            AttachmentSchoolSupervisor::updateOrCreate(
                $rowData,
                []
            );

            $inserted++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'inserted' => $inserted,
            'errors' => $errors,
        ]);
    }

}
