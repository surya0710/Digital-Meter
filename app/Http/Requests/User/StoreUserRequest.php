<?php

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'digits:10'],
            'company' => ['nullable', 'string', 'max:80'],
            'user_role' => ['required', Rule::in($this->assignableRoles())],
            'status' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
            'password' => ['required', 'confirmed', Password::min(6)],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_role' => 'role',
        ];
    }

    public function userAttributes(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'email' => $this->string('email')->toString(),
            'phone' => $this->string('phone')->toString(),
            'company' => $this->filled('company') ? $this->string('company')->toString() : null,
            'user_role' => UserRole::from($this->string('user_role')->toString()),
            'status' => $this->boolean('status'),
            'password' => $this->string('password')->toString(),
        ];
    }

    protected function assignableRoles(): array
    {
        return [
            UserRole::Admin->value,
            UserRole::Guest->value,
            UserRole::User->value,
        ];
    }
}
