<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:1'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->input('email'))->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey(), 60);
            
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if ($user->isLocked()) {
            RateLimiter::hit($this->throttleKey(), 60);
            
            $lockTime = $user->locked_until->diffForHumans();
            throw ValidationException::withMessages([
                'email' => ["Your account is locked until {$lockTime}. Please try again later."],
            ]);
        }

        if (!Hash::check($this->input('password'), $user->password)) {
            $user->incrementFailedAttempts();
            RateLimiter::hit($this->throttleKey(), 60);
            
            $remainingAttempts = 5 - $user->failed_login_attempts;
            $message = $remainingAttempts > 0 
                ? "Invalid credentials. {$remainingAttempts} attempts remaining before account lockout."
                : 'Account has been locked due to too many failed attempts.';
            
            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        
        $user->resetFailedAttempts();
        $user->updateLastLogin();
        
        Auth::login($user, $this->boolean('remember'));
        
        $this->session()->regenerate();
    }

    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
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
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ];
    }
}
