<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuthenticationService $authService
    ) {}

    public function index(): View
    {
        return view('login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        return $this->authService->login($request);
    }

    public function logout(): RedirectResponse
    {
        return $this->authService->logout();
    }
}
