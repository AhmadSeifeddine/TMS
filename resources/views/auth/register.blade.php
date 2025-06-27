<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Profile Image -->
        <div class="mt-4">
            <x-input-label for="profile_image" :value="__('Profile Image (Optional)')" />
            <input id="profile_image" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="file" name="profile_image" accept="image/*" onchange="validateFileSize(this)" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Maximum file size: 2MB. If no image is provided, a default profile image will be used.') }}</p>
            <div id="file-size-error" class="mt-2 text-sm text-red-600 dark:text-red-400" style="display: none;"></div>
            <x-input-error :messages="$errors->get('profile_image')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4" id="registerButton">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function validateFileSize(input) {
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            const errorDiv = document.getElementById('file-size-error');
            const registerButton = document.getElementById('registerButton');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > maxSize) {
                    errorDiv.textContent = 'The selected file is too large. Please choose a file smaller than 2MB.';
                    errorDiv.style.display = 'block';
                    registerButton.disabled = true;
                    registerButton.classList.add('opacity-50', 'cursor-not-allowed');

                    // Clear the file input
                    input.value = '';
                } else {
                    errorDiv.style.display = 'none';
                    registerButton.disabled = false;
                    registerButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                errorDiv.style.display = 'none';
                registerButton.disabled = false;
                registerButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Additional form validation before submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('profile_image');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            if (fileInput.files && fileInput.files[0] && fileInput.files[0].size > maxSize) {
                e.preventDefault();
                const errorDiv = document.getElementById('file-size-error');
                errorDiv.textContent = 'Please select a file smaller than 2MB before submitting.';
                errorDiv.style.display = 'block';
                return false;
            }
        });
    </script>
</x-guest-layout>
