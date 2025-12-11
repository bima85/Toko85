<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $username;
    public $password;
    public $remember = false;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();
        // Try login by username first, then by email as a fallback (users may enter email)
        $attempts = [
            ['username' => $this->username, 'password' => $this->password],
        ];

        if (filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            // user typed an email address, try email as well
            array_unshift($attempts, ['email' => $this->username, 'password' => $this->password]);
        } else {
            // also try email fallback in case username not set but value matches an email
            array_push($attempts, ['email' => $this->username, 'password' => $this->password]);
        }

        foreach ($attempts as $cred) {
            if (Auth::attempt($cred, $this->remember)) {
                session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }

        $this->addError('username', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        // Use Livewire's layout chaining so this component is rendered
        // inside the simple guest layout (which contains the @stack hooks).
        // The `->layout()` method is provided dynamically by Livewire at runtime
        // (static analyzers like PHPStan / Psalm or IDE inspections may flag it as
        // undefined). Silence the inspection here to keep IDE lint output clean.
        /** @noinspection PhpUndefinedMethodInspection */
        // @phpstan-ignore-next-line
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
