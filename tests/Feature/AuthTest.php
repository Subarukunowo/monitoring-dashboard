<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_screens_render(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register_and_defaults_to_user_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'petugas_baru',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'petugas_baru',
            'role' => 'user',
        ]);
    }

    public function test_user_can_login_with_valid_credentials_and_logout(): void
    {
        $user = User::factory()->create([
            'name' => 'admin_uji',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'name' => 'admin_uji',
            'password' => 'wrongpass',
        ])->assertSessionHasErrors('name');

        $this->post('/login', [
            'name' => 'admin_uji',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        // Authenticated user visiting /login or /register redirects to dashboard
        $this->get('/login')->assertRedirect('/dashboard');
        $this->get('/register')->assertRedirect('/dashboard');

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
