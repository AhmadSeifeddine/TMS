<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CompanyEmailDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Check if the request content is empty due to PHP upload limits
        if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $maxSize = ini_get('upload_max_filesize');
            throw ValidationException::withMessages([
                'profile_image' => "The profile image is too large. Maximum file size allowed is {$maxSize}."
            ]);
        }

        // Check if the file was uploaded but exceeds the PHP upload limit
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_FORM_SIZE) {
            $maxSize = ini_get('MAX_FILE_SIZE') ?: ini_get('upload_max_filesize');
            throw ValidationException::withMessages([
                'profile_image' => "The profile image is too large. Maximum file size allowed is {$maxSize}."
            ]);
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
            throw ValidationException::withMessages([
                'profile_image' => $error
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                new CompanyEmailDomain,
            ],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
