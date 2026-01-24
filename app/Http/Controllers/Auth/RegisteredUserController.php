<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\AdministrativeUnit;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
   public function create(): \Illuminate\View\View
{
    // Fetch everything from administrative_units
    $units = \Illuminate\Support\Facades\DB::table('administrative_units')
        ->orderBy('name', 'asc')
        ->get();

    // Pass them to the view. 
    // We map $units to both names so your @foreach loops work as they are.
    return view('auth.register', [
        'departments' => $units,
        'programs'    => $units
    ]);
}
   

public function store(Request $request)
{
    // 1. Validation
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        'role' => ['required', 'in:student,lecturer'],
        
         
        
        // Lecturer specific
        'staff_number' => ['required_if:role,lecturer', 'nullable', 'string', 'unique:lecturers,staff_number'],
        'job_grade' => ['required_if:role,lecturer', 'nullable', 'string'],
         'office_phone' => ['required_if:role,lecturer', 'max:20'], // O
         'department' => ['required_if:role,lecturer', 'string'],
       // Student Specific Validation
        'reg_no' => ['required_if:role,student', 'nullable', 'string', 'unique:students,reg_no'],
        'year_of_study' => ['required_if:role,student', 'nullable', 'string'],
        'program_id' => ['required_if:role,student', 'nullable', 'integer'],
        'phone_number' => ['required_if:role,student', 'max:20'],
    ]);

    // 2. Create the Base User
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => $request->role,
       
    ]);

    // 3. Create Specific Profiles
    if ($request->role === 'lecturer') {
        \App\Models\Lecturer::create([
            'user_id' => $user->id,
            'staff_number' => $request->staff_number,
            'department_id' => $request->department,
            'job_grade' => $request->job_grade,
            'office_phone' => $request->phone_number,
        ]);
    } elseif ($request->role === 'student') {
        \App\Models\Student::create([
            'user_id' => $user->id,
            'reg_no' => $request->reg_no,
            'year_of_study' => $request->year_of_study,
            'program_id' => $request->program_id, // Must be an integer
            'phone_number' => $request->phone_number,
        ]);
    }
    // 4. Trigger Registration Event
    event(new Registered($user));

    // 5. Redirect
    return redirect()->route('login')
        ->with('success', 'Registration successful. Please wait for approval.');
}
}