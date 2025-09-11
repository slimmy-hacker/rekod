<?php
use App\Http\Controllers\auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogbookController;
Route::get('/', function () {
    return view('welcome');
    
});
// Show register form with query parameter (?portal=student)
// Show register form (with portal parameter)
Route::get('/register/{portal}', [RegisteredUserController::class, 'showRegisterForm'])
    ->name('register.portal');

// Handle registration form submission
Route::post('/register/{portal}', [RegisteredUserController::class, 'store'])
    ->name('register.portal.store');

Route::get('/login/{portal}', [AuthenticatedSessionController::class, 'create'])
    ->name('login.portal');

Route::post('/login/{portal}', [AuthenticatedSessionController::class, 'store'])
    ->name('login.portal.store');
Route::get('{portal}/login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::post('{portal}/login', [AuthenticatedSessionController::class, 'store']);


Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/portal/{portal}', [PortalController::class, 'showLoginForm'])->name('auth.portal');
Route::post('/portal/{portal}', [PortalController::class, 'loginOrRegister']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/supervisor', function () {
    return view('supervisor');
})->middleware('auth');

Route::get('/industry', function () {
    return view('industry');
})->middleware('auth');

Route::get('/student/portal', function () {
    return view('student.portal'); 
})->middleware(['auth'])->name('student.portal');

Route::get('/supervisor/portal', function () {
    return view('supervisor.portal'); 
})->middleware(['auth'])->name('supervisor.portal');

Route::get('/admin/portal', function () {
    return view('admin.portal'); 
})->middleware(['auth'])->name('admin.portal');

Route::get('/industry/portal', function () {
    return view('industry.portal'); 
})->middleware(['auth'])->name('industry.portal');

Route::get('/student/attachment-form', [StudentController::class, 'showAttachmentForm'])->name('student.attachment.form');
Route::post('/student/attachment-form', [StudentController::class, 'submitAttachmentForm'])->name('student.attachment.form.submit');
Route::get('/student/reports', [StudentController::class,'reports'])->name('student.reports');
Route::get('/student/logbook', [StudentController::class,'logbook'])->name('student.logbook');
Route::get('/student/results',[StudentController::class,'results'])->name('student.results');


Route::get('/admin/portal', function () {
    return view('admin.portal'); 
})->middleware(['auth'])->name('admin.portal');

Route::middleware(['auth'])->group(function () {


    // Store logbook entry
    Route::post('/student/logbook', [LogbookController::class, 'store'])->name('logbook.store');

    // View all logbook entries
    Route::get('/student/logbook/entries', [LogbookController::class, 'index'])->name('logbook.index');
});
Route::get('/student', function () {
    return redirect()->route('student.portal');
})->name('student');

Route::get('/supervisor', function () {
    return redirect()->route('supervisor.portal');
})->name('supervisor');

Route::get('/industry', function () {
    return redirect()->route('industry.portal');
})->name('industry');
Route::get('/industry', [IndustryController::class, 'index'])->name('industry.portal');


Route::get('/admin', function () {
    return redirect()->route('admin.portal');
})->name('admin');

// Student routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/attachment-form', [StudentController::class, 'showAttachmentForm'])
        ->name('attachment-form');
    
    Route::post('/attachment-form', [StudentController::class, 'storeAttachmentForm'])
        ->name('attachment-form.store');
});
use App\Http\Controllers\SupervisorController;

Route::prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/students-assigned', [SupervisorController::class, 'studentsAssigned'])
        ->name('students-assigned');

    Route::get('/reports', [SupervisorController::class, 'reports'])
        ->name('reports');

    Route::get('/logbook', [SupervisorController::class, 'logbook'])
        ->name('logbook');

    Route::get('/evaluate', [SupervisorController::class, 'evaluate'])
        ->name('evaluate');
});
