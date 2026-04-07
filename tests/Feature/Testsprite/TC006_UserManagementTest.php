<?php

namespace Tests\Feature\Testsprite;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class TC006_UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
    }

    public function test_users_index_is_accessible_by_admin()
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('User');
    }

    public function test_admin_can_create_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'role' => 'pengguna',
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@test.com',
            'role' => 'pengguna',
        ]);
    }

    public function test_cannot_create_user_with_duplicate_username()
    {
        // Create first user
        User::create([
            'name' => 'Existing User',
            'username' => 'existing',
            'email' => 'existing@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);

        // Try to create user with same username
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New User',
            'username' => 'existing', // Duplicate
            'email' => 'new@test.com',
            'password' => 'password123',
            'role' => 'pengguna',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_admin_can_update_user()
    {
        $user = User::create([
            'name' => 'Update Me',
            'username' => 'updateme',
            'email' => 'update@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'username' => 'updated',
            'email' => 'updated@test.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/users');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'admin',
        ]);
    }

    public function test_update_user_without_password_does_not_change_password()
    {
        $originalPassword = bcrypt('originalpass');
        $user = User::create([
            'name' => 'Password Test',
            'username' => 'passwordtest',
            'email' => 'password@test.com',
            'password' => $originalPassword,
            'role' => 'pengguna',
        ]);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'username' => 'passwordtest',
            'email' => 'password@test.com',
            'role' => 'pengguna',
            // No password field
        ]);

        $user->refresh();
        // Password should remain unchanged
        $this->assertEquals($originalPassword, $user->password);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::create([
            'name' => 'Delete Me',
            'username' => 'deleteme',
            'email' => 'delete@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);

        $response = $this->actingAs($this->admin)->delete("/users/{$user->id}");
        $response->assertRedirect('/users');
        
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_their_own_account()
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
        ]);
    }

    public function test_pengguna_cannot_access_user_management()
    {
        $pengguna = User::create([
            'name' => 'Pengguna',
            'username' => 'pengguna',
            'email' => 'pengguna@test.com',
            'password' => bcrypt('password'),
            'role' => 'pengguna',
        ]);

        $response = $this->actingAs($pengguna)->get('/users');
        
        // Role middleware may redirect (302) or return forbidden (403)
        $this->assertTrue(
            $response->isRedirect() || 
            $response->isForbidden() ||
            $response->isClientError()
        );
    }
}
