<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->ensureIsNotRateLimited();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u'
            ],
            'email' => [
                'required', 
                'email:rfc', 
                'max:255', 
                'unique:users,email'
            ],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'terms' => ['accepted'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Your name must be at least 2 characters long.',
            'name.regex' => 'The name may only contain letters, spaces, hyphens, apostrophes, and dots.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
            'terms.accepted' => 'You must accept the terms and conditions to register.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'terms' => 'terms and conditions',
        ];
    }

    /**
     * Ensure the registration request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return 'register|'.$this->ip();
    }
}
