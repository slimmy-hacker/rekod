
<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\IndurstrialSupervisorController;
use App\Http\Controllers\CompanyController;



Route::get('/', function () {
    if (Auth::check() && Auth::user()->role) {
        return redirect()->route(Auth::user()->role . '.portal');
    }
    return view('welcome');
})->name('welcome');


require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/companies', [CompanyController::class, 'companies'])->name('companies');
    Route::post('/companies', [CompanyController::class, 'storeCompany'])->name('companies.store');

    Route::get('get-company-industrial-supervisors/{id}', [IndurstrialSupervisorController::class, 'getCompanyIndustrialSupervisors'])->name('get_company_industrial_supervisors');
});


Route::middleware(['portal:student'])
    ->prefix('students')
    ->group(base_path('routes/student.php'));


Route::middleware(['portal:admin'])
    ->prefix('admin')
    ->group(base_path('routes/admin.php'));

    Route::middleware('guest')->group(function () {
        Route::get('/register/select-portal', function () {
            return view('auth.select-portal');
        })->name('register.select.portal');

        Route::get('/register/{portal}', [RegisteredUserController::class, 'showPortalForm'])
            ->where('portal', 'student|supervisor|industry|company')
            ->name('register.portal');

        Route::post('/student/register', [RegisteredUserController::class, 'storeStudent'])
                        ->name('register.portal.student');

        Route::post('/register/{portal}', [RegisteredUserController::class, 'storePortal'])
            ->where('portal', 'student|supervisor|industry|company')
            ->name('register.portal.store');


        // --- Test route ---

        Route::get('/login/{portal}', [AuthenticatedSessionController::class, 'create'])
    ->name('portal.login');
    });
