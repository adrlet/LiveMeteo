<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => ['required', 'string', 'max:32'],
            'password' => ['required', 'string'], 
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors()]);
        throw (new ValidationException($validator, $response));
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $error = $this->ensureIsNotRateLimited();
        if($error != NULL)
            return $error;

        if (! Auth::attempt($this->only('login', 'password'), $this->boolean('remember'))) {

            RateLimiter::hit($this->throttleKey());
            return [ 'errors' => ['login' => [0 => trans('auth.failed')]]];
        }

        RateLimiter::clear($this->throttleKey());
        return NULL;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) 
            return NULL;

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        return 
        [ 'errors' => 
            [ 'email' => 
                [ 
                    0 => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60),]),
                ]
            ]
        ];
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
