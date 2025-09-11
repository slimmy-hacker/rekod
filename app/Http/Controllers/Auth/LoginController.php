<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        
        $portal = $request->route('portal') ?? $request->query('portal');
        session(['portal' => $portal]);

        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            
            $portal = session('portal');

           
            switch ($portal) {
                case 'student':
                    return redirect()->route('student.portal');
                case 'supervisor':
                    return redirect()->route('supervisor.portal');
                case 'industry':
                    return redirect()->route('industry.portal');
                default:
                    Auth::logout();
                    return redirect('/')->withErrors(['portal' => 'Invalid portal.']);
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

   public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

   
    $portal = session('portal', 'student');

    return redirect()->route('login', ['portal' => $portal]);
}

}
