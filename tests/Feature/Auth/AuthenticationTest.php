<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_redirects_to_welcome(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect(route('welcome', ['auth' => 'login']));
    }

    public function test_admin_can_authenticate_with_password(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            '_auth_form' => 'admin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_owner_cannot_authenticate_with_password(): void
    {
        $user = User::factory()->create([
            'role' => 'owner',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            '_auth_form' => 'admin',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            '_auth_form' => 'admin',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
