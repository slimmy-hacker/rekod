<?php
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\AttarchmentScheduleController;
use App\Http\Controllers\AttachmentSchoolSupervisorController;
use App\Http\Controllers\AttachmentStudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LocationController;

use Illuminate\Support\Facades\Route;


Route::get('portal', function () {
    return view('admin.portal');
})->name('admin.portal');

Route::get('attarchment-schedules/index', [AttarchmentScheduleController::class, 'index'])->name('admin.attachment_schedules.index');


Route::post('attarchment-schedules/store', [AttarchmentScheduleController::class, 'store'])->name('admin.attachment_schedule.store');
Route::get('locations', [LocationController::class, 'index'])->name('locations');
    Route::get('locations/upload', [LocationController::class, 'upload'])->name('locations.upload');

Route::get('attarchment-schedules-supervisors/index', [AttachmentSchoolSupervisorController::class, 'index'])->name('admin.attachment_schedules.supervisors.index');

Route::post('attachment-schedules-supervisors/upload', [AttachmentSchoolSupervisorController::class, 'upload'])->name('admin.attachment_schedules.supervisors.upload');

Route::get('attarchment-schedules-students/index', [AttachmentStudentController::class, 'index'])->name('admin.attachment_student.index');
Route::get('locations/upload', [LocationController::class, 'upload'])->name('locations.upload');
    // or if it's a POST route:
    Route::post('locations/upload', [LocationController::class, 'upload'])->name('locations.upload');
Route::post('attachment-schedules-students/upload', [AttachmentStudentController::class, 'upload'])->name('admin.attachment_student.upload');
Route::get('budgets', [AdminController::class, 'budgets'])->name('admin.budgets');
Route::post('budgets', [AdminController::class, 'storeBudget'])->name('admin.budgets.store');

Route::get('budgets/{id}', [AdminController::class, 'showBudget'])
    ->name('admin.budgets.show');
    Route::delete('budgets/{id}', [AdminController::class, 'destroyBudget'])->name('admin.budgets.destroy');
