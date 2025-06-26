<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CompanyEmailDomain;
use App\Traits\FlashMessages;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use FlashMessages;
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, new CompanyEmailDomain],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->hasFile('profile_image')) {
            $user->addMediaFromRequest('profile_image')
                ->usingFileName(time() . '_' . $request->file('profile_image')->getClientOriginalName())
                ->toMediaCollection('profile_images');
        } else {
            $user->addMediaFromUrl('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF')
                ->usingFileName('default_' . time() . '.png')
                ->toMediaCollection('profile_images');
        }

        event(new Registered($user));

        Auth::login($user);

        $this->flashSuccess('Welcome to ' . config('app.name') . '! Your account has been created successfully.');

        return redirect(route('dashboard', absolute: false));
    }
}
