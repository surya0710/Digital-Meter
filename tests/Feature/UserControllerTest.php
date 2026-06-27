<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): User
    {
        $admin = User::factory()->admin()->create([
            'password' => 'password',
        ]);

        $this->actingAs($admin);

        return $admin;
    }

    public function test_guest_role_cannot_access_user_list(): void
    {
        $guest = User::factory()->guest()->create();

        $this->actingAs($guest)
            ->get(route('users.list'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_user_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.list'))
            ->assertForbidden();
    }

    public function test_admin_can_view_user_list(): void
    {
        $this->authenticateAdmin();
        User::factory()->count(2)->create();

        $response = $this->get(route('users.list'));

        $response->assertOk();
        $response->assertViewIs('users');
        $response->assertViewHas('users');
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->authenticateAdmin();

        $response = $this->get(route('users.createform'));

        $response->assertOk();
        $response->assertViewIs('users-create');
    }

    public function test_admin_can_create_user_with_valid_data(): void
    {
        $this->authenticateAdmin();

        $payload = [
            'name' => 'Jane Operator',
            'email' => 'jane@example.com',
            'phone' => '9876543210',
            'company' => '3Elabs',
            'user_role' => UserRole::Guest->value,
            'status' => '1',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->post(route('users.create'), $payload);

        $response->assertRedirect(route('users.list'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'phone' => '9876543210',
            'company' => '3Elabs',
            'user_role' => UserRole::Guest->value,
            'status' => 1,
        ]);
    }

    public function test_create_user_persists_inactive_status(): void
    {
        $this->authenticateAdmin();

        $payload = [
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'phone' => '9876543211',
            'user_role' => UserRole::User->value,
            'status' => '0',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $this->post(route('users.create'), $payload);

        $this->assertDatabaseHas('users', [
            'email' => 'inactive@example.com',
            'status' => 0,
        ]);
    }

    public function test_create_user_validation_errors(): void
    {
        $this->authenticateAdmin();

        $response = $this->from(route('users.createform'))
            ->post(route('users.create'), []);

        $response->assertRedirect(route('users.createform'));
        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone',
            'user_role',
            'status',
            'password',
        ]);
    }

    public function test_create_user_rejects_duplicate_email(): void
    {
        $this->authenticateAdmin();
        $existing = User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->from(route('users.createform'))->post(route('users.create'), [
            'name' => 'Duplicate',
            'email' => $existing->email,
            'phone' => '9876543212',
            'user_role' => UserRole::User->value,
            'status' => '1',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('users.createform'));
        $response->assertSessionHasErrors('email');
    }
}
