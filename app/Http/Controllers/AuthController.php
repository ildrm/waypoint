<?php

namespace App\Http\Controllers;

use App\Domain\RunAccess;
use App\Models\AssessmentRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class AuthController
{
    public function login()
    {
        return view('auth.login');
    }

    public function store(Request $r)
    {
        $d = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($d + ['enabled' => true], false)) {
            throw ValidationException::withMessages(['email' => 'These credentials could not be verified.']);
        }if (count(auth()->user()->capabilities ?? []) > 0) {
            Auth::logout();
            $r->session()->invalidate();
            $r->session()->regenerateToken();

            return redirect('/admin/login');
        }$r->session()->regenerate();

        return redirect()->intended(route('runs.index'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function link(Request $r, AssessmentRun $run, RunAccess $access)
    {
        abort_unless(auth()->check(), 401);
        $access->assert($run);
        abort_unless($r->boolean('confirm'), 422);
        $run->update(['user_id' => auth()->id(), 'resume_token_hash' => null, 'expires_at' => null]);

        return redirect()->route('runs.show', $run);
    }
}
