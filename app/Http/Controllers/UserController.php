<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Services\User\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $users
    ) {}

    public function list(): View
    {
        return view('users', [
            'users' => $this->users->paginatedList(),
        ]);
    }

    public function createForm(): View
    {
        return view('users-create');
    }

    public function create(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create($request->userAttributes());

        return redirect()
            ->route('users.list')
            ->with('success', 'User created successfully.');
    }
}
