
<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

        Route::post('/register/{portal}', [RegisteredUserController::class, 'storePortal'])
            ->where('portal', 'student|supervisor|industry|company')
            ->name('register.portal.store');

        // --- Test route ---
        Route::get('/test-tailwind', function () {
            return view('test-tailwind');
        });
    });
