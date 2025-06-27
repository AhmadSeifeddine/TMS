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
        // Check if the request content is empty due to PHP upload limits
        if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $maxSize = ini_get('upload_max_filesize');
            return back()->withErrors([
                'profile_image' => "The profile image is too large. Maximum file size allowed is {$maxSize}."
            ])->withInput();
        }

        // Check if the file was uploaded but exceeds the PHP upload limit
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_FORM_SIZE) {
            $maxSize = ini_get('MAX_FILE_SIZE') ?: ini_get('upload_max_filesize');
            return back()->withErrors([
                'profile_image' => "The profile image is too large. Maximum file size allowed is {$maxSize}."
            ])->withInput();
        }

        // Check for other upload errors
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'The profile image is too large. Maximum file size allowed is ' . ini_get('upload_max_filesize') . '.',
                UPLOAD_ERR_FORM_SIZE => 'The profile image is too large.',
                UPLOAD_ERR_PARTIAL => 'The profile image was only partially uploaded. Please try again.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder for file upload.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
            ];

            $error = $errorMessages[$_FILES['profile_image']['error']] ?? 'An error occurred during file upload.';
            return back()->withErrors([
                'profile_image' => $error
            ])->withInput();
        }

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
