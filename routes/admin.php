<?php
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\AttarchmentScheduleController;
use App\Http\Controllers\AttachmentSchoolSupervisorController;
use App\Http\Controllers\AttachmentStudentController;
use Illuminate\Support\Facades\Route;


Route::get('portal', function () {
    return view('admin.portal');
})->name('admin.portal');

Route::get('attarchment-schedules/index', [AttarchmentScheduleController::class, 'index'])->name('admin.attachment_schedules.index');


Route::post('attarchment-schedules/store', [AttarchmentScheduleController::class, 'store'])->name('admin.attachment_schedule.store');

Route::get('attarchment-schedules-supervisors/index', [AttachmentSchoolSupervisorController::class, 'index'])->name('admin.attachment_schedules.supervisors.index');

Route::post('attachment-schedules-supervisors/upload', [AttachmentSchoolSupervisorController::class, 'upload'])->name('admin.attachment_schedules.supervisors.upload');

Route::get('attarchment-schedules-students/index', [AttachmentStudentController::class, 'index'])->name('admin.attachment_student.index');

Route::post('attachment-schedules-students/upload', [AttachmentStudentController::class, 'upload'])->name('admin.attachment_student.upload');
