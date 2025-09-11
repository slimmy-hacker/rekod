<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function showRegisterForm($portal)
    {
        // Allowed portals
        $allowed = ['student', 'supervisor', 'industry', 'admin'];

        if (!in_array($portal, $allowed)) {
            abort(404);
        }

        // Each portal has its own register file
        return view("auth.{$portal}_register", compact('portal'));
    }

    public function store(Request $request, $portal)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'portal'   => $portal, // save which portal they registered under
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on portal
        switch ($portal) {
            case 'student':
                return redirect()->route('student.portal');
            case 'supervisor':
                return redirect()->route('supervisor.portal');
            case 'industry':
                return redirect()->route('industry.portal');
            case 'admin':
                return redirect()->route('admin.portal');
            default:
                return redirect('/');
        }
    }
}
