<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DemoLoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(DemoLoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! $user->password || ! Hash::check($request->string('password'), $user->password)) {
            return back()->withErrors(['email' => 'Email atau password demo tidak valid.'])->onlyInput('email');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return $user->workspaceMemberships()->where('status','ACTIVE')->exists()
            ? redirect()->intended(route('home'))
            : redirect()->route('onboarding.workspace');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
