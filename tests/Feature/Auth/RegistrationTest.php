<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_redirects_to_welcome(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect(route('welcome', ['auth' => 'register']));
    }

    public function test_email_password_registration_is_disabled(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('welcome', ['auth' => 'register']));
        $this->assertDatabaseMissing('user', ['email' => 'test@example.com']);
    }
}
