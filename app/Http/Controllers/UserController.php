<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function showRegisterForm(Request $request)
    {
        $portal = $request->query('portal');

        if (!in_array($portal, ['student', 'supervisor', 'industry','Admin'])) {
            abort(404); 
        }

        session(['portal' => $portal]);

        return view('auth.login', compact('portal'));
    }

    public function loginOrRegister(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $portal = session('portal');

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route("dashboard.$portal");
            } else {
                return back()->withErrors(['password' => 'Incorrect password']);
            }
        } else {
           
            $user = User::create([
                'name' => $request->name ?? 'Unnamed',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'portal' => $portal,
            ]);

            Auth::login($user);
            return redirect()->route("dashboard.$portal");
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
