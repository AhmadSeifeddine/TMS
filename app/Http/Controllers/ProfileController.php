<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Traits\FlashMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use FlashMessages;
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $profileUpdated = false;
        $imageUpdated = false;

        // Handle profile image update
        if ($request->hasFile('profile_image')) {
            try {
                // Delete existing profile image
                $user->clearMediaCollection('profile_images');

                // Add new profile image
                $user->addMediaFromRequest('profile_image')
                      ->usingFileName(time() . '_' . $request->file('profile_image')->getClientOriginalName())
                      ->toMediaCollection('profile_images');

                $imageUpdated = true;
            } catch (\Exception $e) {
                $this->flashError('Failed to update profile image. Please try again.');
                return Redirect::route('profile.edit');
            }
        }

        // Flash appropriate success message
        if ($imageUpdated && $user->wasChanged()) {
            $this->flashSuccess('Profile information and image updated successfully!');
        } elseif ($imageUpdated) {
            $this->flashSuccess('Profile image updated successfully!');
        } elseif ($user->wasChanged()) {
            $this->flashSuccess('Profile information updated successfully!');
        } else {
            $this->flashInfo('No changes were made to your profile.');
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
