<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create($portal)
    {
        return view('auth.login', compact('portal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $portal = $request->route('portal');

        if (!in_array($portal, ['student', 'supervisor', 'industry', 'admin'])) {
            abort(403, 'Invalid portal.');
        }

        if (!Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

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

   public function destroy(Request $request)
{
    auth()->guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $portal = session('portal', 'student');

    return redirect()->route('login', ['portal' => $portal]);
}
}