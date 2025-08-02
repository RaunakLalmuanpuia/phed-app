<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    //
    public function edit(Request $request): Response
    {
        return Inertia::render('Backend/Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data=$this->validate($request,[
            'name' => ['required'],
            'email'=>['required',Rule::unique('users','email')->ignore(\auth()->id())],
            'mobile'=>['required','digits:10',Rule::unique('users','mobile')->ignore(\auth()->id())],
            'designation'=>['nullable','string'],
        ]);
        $request->user()->update($data);

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function editPassword(Request $request)
    {
        return \inertia('Backend/Profile/ChangePassword', [
            'data' => $request->user(),
        ]);

    }

    public function updatePassword(Request $request)
    {
        $data=$this->validate($request, [
            'old_password'=>['required',],
            'password' => ['required','min:6','confirmed'],
        ]);

        $user = $request->user();
        $match=Hash::check($data['old_password'], $user->password);

        if (!$match) {
            return back()->withErrors(['old_password' => 'Invalid password']);
        }
        $user->password = Hash::make($data['password']);
        $user->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login');
    }
}
