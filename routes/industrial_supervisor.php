<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndustrialSupervisorApprovalController;
use App\Http\Controllers\Auth\RegisteredUserController;



Route::post('/register/industrial_supervisor', [RegisteredUserController::class, 'industrial_supervisor'])
    ->name('register.industrial_supervisor');
// Industrial Supervisor Portal
Route::get('/portal', function () {
    return view('industry.portal');
})->name('industrial_supervisor.portal');

// Approvals
Route::get('/approvals', [IndustrialSupervisorApprovalController::class, 'index'])
    ->name('industrial_supervisor.approvals.index');

Route::post('/approvals/store', [IndustrialSupervisorApprovalController::class, 'store'])
    ->name('industrial_supervisor.approvals.store');
