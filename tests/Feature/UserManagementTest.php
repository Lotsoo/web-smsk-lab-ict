<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login()
    {
        $user = User::factory()->create([
            'username' => 'activeuser',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'username' => 'activeuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_deactivated_user_cannot_login()
    {
        $user = User::factory()->create([
            'username' => 'inactiveuser',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'username' => 'inactiveuser',
            'password' => 'password123',
        ]);

        $response->assertSessionHas('loginError');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_user_list()
    {
        $admin = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get('/dashboard/users');

        $response->assertStatus(200);
        $response->assertSee('Kelola User');
    }

    public function test_can_create_new_user()
    {
        $admin = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => 'Budi Santoso',
            'username' => 'budi',
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'budi',
            'name' => 'Budi Santoso',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_can_update_user()
    {
        $admin = User::factory()->create(['is_active' => true]);
        $targetUser = User::factory()->create([
            'name' => 'Old Name',
            'username' => 'oldusername',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put("/dashboard/users/{$targetUser->id}", [
            'name' => 'Updated Name',
            'username' => 'updatedusername',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'username' => 'updatedusername',
        ]);
    }

    public function test_can_toggle_user_status()
    {
        $admin = User::factory()->create(['is_active' => true]);
        $targetUser = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch("/dashboard/users/{$targetUser->id}/toggle-status");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'is_active' => false,
        ]);
    }

    public function test_user_cannot_deactivate_self()
    {
        $admin = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch("/dashboard/users/{$admin->id}/toggle-status");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_delete_self()
    {
        $admin = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->delete("/dashboard/users/{$admin->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_can_delete_other_user()
    {
        $admin = User::factory()->create(['is_active' => true]);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin)->delete("/dashboard/users/{$targetUser->id}");

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    public function test_superadmin_cannot_be_deactivated()
    {
        $superadminOwner = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        $superadminTarget = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);

        // Toggle status attempt by superadmin on another superadmin
        $response = $this->actingAs($superadminOwner)->patch("/dashboard/users/{$superadminTarget->id}/toggle-status");
        $response->assertSessionHas('error', 'Akun Superadmin tidak dapat dinonaktifkan!');
        $this->assertDatabaseHas('users', [
            'id' => $superadminTarget->id,
            'is_active' => true,
        ]);

        // Update attempt with is_active = 0
        $response = $this->actingAs($superadminOwner)->put("/dashboard/users/{$superadminTarget->id}", [
            'name' => $superadminTarget->name,
            'username' => $superadminTarget->username,
            'role' => 'superadmin',
            'is_active' => '0',
        ]);
        $response->assertSessionHas('error', 'Akun Superadmin tidak dapat dinonaktifkan!');
        $this->assertDatabaseHas('users', [
            'id' => $superadminTarget->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_edit_or_modify_superadmin()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);

        // Admin tries to view edit form of superadmin
        $response = $this->actingAs($admin)->get("/dashboard/users/{$superadmin->id}/edit");
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengedit akun Superadmin!');

        // Admin tries to update superadmin
        $response = $this->actingAs($admin)->put("/dashboard/users/{$superadmin->id}", [
            'name' => 'Hacked Name',
            'username' => $superadmin->username,
            'role' => 'admin',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengubah akun Superadmin!');

        // Admin tries to toggle status of superadmin
        $response = $this->actingAs($admin)->patch("/dashboard/users/{$superadmin->id}/toggle-status");
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengubah status akun Superadmin!');

        // Admin tries to delete superadmin
        $response = $this->actingAs($admin)->delete("/dashboard/users/{$superadmin->id}");
        $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk menghapus akun Superadmin!');
    }

    public function test_user_cannot_change_other_users_password()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        $targetAdmin = User::factory()->create(['role' => 'admin', 'password' => Hash::make('oldpassword'), 'is_active' => true]);

        $response = $this->actingAs($superadmin)->put("/dashboard/users/{$targetAdmin->id}", [
            'name' => 'Target Admin',
            'username' => $targetAdmin->username,
            'password' => 'newpassword123',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('error', 'Anda tidak diperbolehkan mengubah password pengguna lain!');
        $targetAdmin->refresh();
        $this->assertTrue(Hash::check('oldpassword', $targetAdmin->password));
    }

    public function test_user_can_change_own_password()
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => Hash::make('oldpassword'), 'is_active' => true]);

        $response = $this->actingAs($user)->put("/dashboard/users/{$user->id}", [
            'name' => $user->name,
            'username' => $user->username,
            'password' => 'newpassword123',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_regular_admin_created_user_defaults_to_admin_role()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => 'Attempt Superadmin',
            'username' => 'attemptsadmin',
            'password' => 'password123',
            'role' => 'superadmin', // attempted bypass
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'attemptsadmin',
            'role' => 'admin', // forced to admin
        ]);
    }

    public function test_superadmin_can_create_superadmin_user()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);

        $response = $this->actingAs($superadmin)->post('/dashboard/users', [
            'name' => 'New Superadmin',
            'username' => 'newsuperadmin',
            'password' => 'password123',
            'role' => 'superadmin',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'newsuperadmin',
            'role' => 'superadmin',
        ]);
    }
}
