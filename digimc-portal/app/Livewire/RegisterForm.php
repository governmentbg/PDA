<?php

namespace App\Livewire;

use Alert;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Services\UserService;
use Illuminate\Validation\Rules\Password;

class RegisterForm extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $password_confirmation;
    public $wants_notifications = false;
    public $subscribed_news = false;
    public $subscribed_weekly = false;

    public function updated($propertyName): void
    {

    }

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => __('validation.required'),
            'last_name.required' => __('validation.required'),
            'email.required' => __('validation.required'),
            'email.email' => __('validation.required'),
            'email.unique' => __('validation.custom.email.unique'),
            'password.required' => __('validation.required'),
            'password.match' => __('validation.required'),
            'password.min_eight' => __('validation.required'),
        ];
    }


    public function passwordStrength(): string
    {
        $password = $this->password ?? '';

        if (empty($password)) {
            return 'слаба';
        }

        $score = 0;
        $score += strlen($password) >= 8 ? 1 : 0;
        $score += preg_match('/[A-Z]/', $password) ? 1 : 0;
        $score += preg_match('/[0-9]/', $password) ? 1 : 0;
        $score += preg_match('/[\W]/', $password) ? 1 : 0;

        if ($score <= 1) return __('validation.password.weak');
        if ($score <= 3) return __('validation.password.normal');
        return __('validation.password.strong');
    }


    public function register(UserService $userService): void
    {
        $this->validate();

        $userData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'wants_notifications' => $this->wants_notifications,
            'subscribed_news' => $this->subscribed_news,
            'subscribed_weekly' => $this->subscribed_weekly,
        ];

        $userService->createUser($userData);

        Alert::success(__('profile.activation.alert_title'), __('profile.activation.alert_message'));

        $this->redirectRoute('auth.login');
    }

    public function render()
    {
        return view('livewire.register-form');
    }
}
