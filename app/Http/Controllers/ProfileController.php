<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            ]);
        }

        $kelasOptions = Kelas::with(['school', 'tahunAjaran'])->orderBy('school_id')->orderBy('name')->get();

        return view('pages.profile.edit', compact('user', 'profile', 'kelasOptions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nim'       => ['required', 'string', 'max:50'],
            'gender'    => ['required', 'in:Laki-laki,Perempuan'],
            'kelas_id'  => ['required', 'exists:kelas,id'],
            'avatar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $profile = $user->profile;

        $data = $request->only(['full_name', 'nim', 'gender', 'kelas_id']);

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

        return redirect()->route('dashboard')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hasPassword = !is_null($user->password);

        $rules = [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($hasPassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('profile.edit')
            ->with('success', $hasPassword ? 'Password berhasil diubah!' : 'Password berhasil dibuat! Kamu sekarang bisa login dengan email dan password.');
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