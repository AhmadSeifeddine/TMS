<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information, profile picture, and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data" id="profileUpdateForm">
        @csrf
        @method('patch')

        <!-- Profile Image -->
        <div>

            <div class="mt-2 flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($user->getFirstMediaUrl('profile_images'))
                        <img class="h-20 w-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
                            src="{{ $user->getFirstMediaUrl('profile_images') }}"
                            alt="{{ $user->name }}"
                            id="profile-preview">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center border-2 border-gray-400 dark:border-gray-500" id="profile-preview">
                            <span class="text-gray-600 dark:text-gray-400 font-medium text-xl">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <input type="file"
                        id="profile_image"
                        name="profile_image"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/20 dark:file:text-blue-400
                                dark:hover:file:bg-blue-900/30"
                        onchange="validateAndPreviewImage(this)">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, GIF up to 2MB
                    </p>
                    <div id="profile-file-size-error" class="mt-2 text-sm text-red-600 dark:text-red-400" style="display: none;"></div>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button id="profileSaveButton">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        function validateAndPreviewImage(input) {
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            const errorDiv = document.getElementById('profile-file-size-error');
            const saveButton = document.getElementById('profileSaveButton');
            const preview = document.getElementById('profile-preview');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Check file size
                if (file.size > maxSize) {
                    errorDiv.textContent = 'The selected file is too large. Please choose a file smaller than 2MB.';
                    errorDiv.style.display = 'block';
                    saveButton.disabled = true;
                    saveButton.classList.add('opacity-50', 'cursor-not-allowed');

                    // Clear the file input
                    input.value = '';
                } else {
                    errorDiv.style.display = 'none';
                    saveButton.disabled = false;
                    saveButton.classList.remove('opacity-50', 'cursor-not-allowed');

                    // Preview the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img class="h-20 w-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
                                                  src="${e.target.result}"
                                                  alt="Profile Preview">`;
                    }
                    reader.readAsDataURL(file);
                }
            } else {
                errorDiv.style.display = 'none';
                saveButton.disabled = false;
                saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Additional form validation before submission
        document.getElementById('profileUpdateForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('profile_image');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (fileInput.files && fileInput.files[0] && fileInput.files[0].size > maxSize) {
                e.preventDefault();
                const errorDiv = document.getElementById('profile-file-size-error');
                errorDiv.textContent = 'Please select a file smaller than 2MB before submitting.';
                errorDiv.style.display = 'block';
                return false;
            }
        });

        // Legacy function for backward compatibility (if needed elsewhere)
        function previewImage(input) {
            validateAndPreviewImage(input);
        }
    </script>
</section>
