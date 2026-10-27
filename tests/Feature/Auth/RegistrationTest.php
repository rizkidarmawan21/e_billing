<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_guests_cannot_create_privileged_accounts(): void
    {
        foreach (['admin', 'owner'] as $role) {
            $this->post('/register', [
                'name' => 'Untrusted User',
                'email' => $role.'@example.com',
                'role' => $role,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])->assertNotFound();
        }

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }
}
