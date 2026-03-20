<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            $profile = $user->profile()->create([
                'full_name' => $user->username,
                'school_name' => 'SMKN 4 Bandung',
            ]);
        }

        return view('pages.profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'kelas' => ['required', 'in:XI PPLG 1,XI PPLG 2,XI PPLG 3'],
            'school_name' => ['required', 'in:SMKN 4 Bandung'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $profile = $user->profile;

        $data = $request->only(['full_name', 'nim', 'gender', 'kelas', 'school_name']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!$profile) {
            $user->profile()->create($data);
        } else {
            $profile->update($data);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus avatar jika ada
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}