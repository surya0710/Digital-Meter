<?php

namespace App\Services\User;

use App\Models\User;

class UserService
{
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function paginatedList(int $perPage = 10)
    {
        return User::query()
            ->latest()
            ->paginate($perPage);
    }
}
