<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PortalController extends Controller
{
    public function showLoginForm($portal)
    {
        if (!in_array($portal, ['student', 'supervisor', 'industry','admin'])) {
            abort(404);
        }

        return view('auth.portal-form', compact('portal'));
    }

    public function handleAuth(Request $request, $portal)
    {
        if (!in_array($portal, ['student', 'supervisor', 'industry','admin'])) {
            abort(404);
        }

        if ($request->has('login')) {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route($portal . '.portal'); 
            }

            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        if ($request->has('register')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);
            return redirect()->route($portal . '.portal');
        }

        return back()->withErrors(['action' => 'Invalid form submission']);
    }
}
