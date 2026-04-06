<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function authenticated_user_cannot_view_login_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function login_requires_email()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_requires_password()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_view_user_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /** @test */
    public function non_admin_cannot_view_user_list()
    {
        $user = User::factory()->create(['role' => 'pengguna']);

        $response = $this->actingAs($user)->get('/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'pengguna',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => $user->role,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->delete("/users/{$user->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
