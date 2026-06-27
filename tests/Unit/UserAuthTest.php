<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_uses_admin_layout(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->usesAdminLayout());
        $this->assertFalse($user->usesSimplifiedLayout());
    }

    public function test_login_redirect_targets_device_list_for_non_admin(): void
    {
        $customer = User::factory()->customer()->create();

        $this->assertSame(
            route('devices.list', absolute: false),
            $customer->loginRedirectUrl()
        );
    }

    public function test_login_redirect_targets_dashboard_for_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame('/dashboard', $admin->loginRedirectUrl());
    }

    public function test_role_label_uses_enum(): void
    {
        $user = User::factory()->create(['user_role' => UserRole::Admin]);

        $this->assertSame('Admin', $user->roleLabel());
    }
}
