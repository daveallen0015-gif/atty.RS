<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\GitHubImageService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'profile' => $request->user()->lawyerProfile,
        ]);
    }

    public function update(Request $request, GitHubImageService $images): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'professional_title' => ['nullable', 'string', 'max:255'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string'],
            'education' => ['nullable', 'string', 'max:12000'],
            'professional_experience' => ['nullable', 'string', 'max:12000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:50'],
            'public_email' => ['nullable', 'email', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        $profile = $user->lawyerProfile()->firstOrNew();
        $profile->fill(collect($validated)->except(['name', 'email', 'photo'])->all());

        if ($request->hasFile('photo')) {
            $previousPhoto = $profile->photo_path;
            $newPhoto = $images->upload($request->file('photo'), 'lawyer-profile');
            $profile->photo_path = $newPhoto;
            $profile->save();
            $images->delete($previousPhoto);
        } else {
            $profile->save();
        }

        return redirect()->route('admin.profile.edit')->with('status', 'Profile updated.');
    }
}
