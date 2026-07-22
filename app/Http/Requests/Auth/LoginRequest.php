<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Login email/password HANYA untuk admin.
     * Owner wajib Google; jangan izinkan password owner lewat sini.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = $this->string('email')->toString();
        $user = User::where('email', $email)->first();

        // Owner / non-admin: tolak sebelum attempt (pesan jelas + tanpa bocorkan hash).
        if ($user && $user->role !== 'admin') {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Pemilik kos wajib masuk dengan Google. Form ini hanya untuk admin.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user && $user->role === 'admin') {
                \App\Services\LoggerService::log(
                    'login_failed',
                    'Gagal login: Password salah',
                    $user,
                    null,
                    null,
                    $user
                );
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Double-check setelah attempt (defense in depth).
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Pemilik kos wajib masuk dengan Google. Form ini hanya untuk admin.',
            ]);
        }

        if (Auth::user()->status === 'inactive') {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun admin dinonaktifkan. Hubungi pemilik kos.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Gagal login admin → kembali ke landing (modal admin).
     */
    protected function getRedirectUrl(): string
    {
        return route('welcome', ['auth' => 'admin']);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
