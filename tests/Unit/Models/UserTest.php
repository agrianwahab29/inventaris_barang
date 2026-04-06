<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_hides_password_when_serialized()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = new User();
        $expectedFillable = ['name', 'username', 'email', 'password', 'role'];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengguna = User::factory()->create(['role' => 'pengguna']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('pengguna', $pengguna->role);
    }

    /** @test */
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'test@example.com']);
    }
}
