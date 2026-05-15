<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this
            ->actingAs($this->admin)
            ->getJson('/admin/users');

        $response->assertOk()
            ->assertJsonCount(4, 'data'); // 3 + 1 admin
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson('/admin/users', [
                'name' => 'Новый Ученик',
                'email' => 'student@test.com',
                'role' => 'user',
                'password' => 'password123',
            ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Пользователь успешно создан.');

        $this->assertDatabaseHas('users', [
            'name' => 'Новый Ученик',
            'email' => 'student@test.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['role' => 'user', 'name' => 'Old Name']);

        $response = $this
            ->actingAs($this->admin)
            ->putJson("/admin/users/{$user->id}", [
                'name' => 'Updated Name',
                'email' => $user->email,
                'role' => 'expert',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Пользователь успешно обновлён.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'expert',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin)
            ->deleteJson("/admin/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Пользователь успешно удалён.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_change_user_role_inline(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this
            ->actingAs($this->admin)
            ->patchJson("/admin/users/{$user->id}/role", [
                'role' => 'expert',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Роль пользователя обновлена.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'expert',
        ]);
    }

    public function test_admin_can_toggle_user_block(): void
    {
        $user = User::factory()->create(['is_blocked' => false]);

        // Заблокировать
        $response = $this
            ->actingAs($this->admin)
            ->patchJson("/admin/users/{$user->id}/block");

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_blocked' => true]);

        // Разблокировать
        $response = $this
            ->actingAs($this->admin)
            ->patchJson("/admin/users/{$user->id}/block");

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_blocked' => false]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->deleteJson("/admin/users/{$this->admin->id}");

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Нельзя удалить самого себя.');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'user']);

        $response = $this
            ->actingAs($user)
            ->getJson('/admin/users');

        $response->assertForbidden();
    }

    public function test_admin_can_search_users(): void
    {
        User::factory()->create(['name' => 'Иван Петров', 'email' => 'ivan@test.com']);
        User::factory()->create(['name' => 'Мария Сидорова', 'email' => 'maria@test.com']);

        $response = $this
            ->actingAs($this->admin)
            ->getJson('/admin/users?search=Иван');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Иван Петров');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        User::factory()->count(2)->create(['role' => 'user']);
        User::factory()->count(3)->create(['role' => 'expert']);

        $response = $this
            ->actingAs($this->admin)
            ->getJson('/admin/users?role=expert');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_validation_fails_when_creating_user_with_invalid_data(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->postJson('/admin/users', [
                'name' => '',
                'email' => 'not-email',
                'role' => 'invalid-role',
                'password' => '123',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'role', 'password']);
    }

    public function test_validation_fails_when_updating_user_with_duplicate_email(): void
    {
        $existing = User::factory()->create(['email' => 'existing@test.com']);
        $target = User::factory()->create(['email' => 'target@test.com']);

        $response = $this
            ->actingAs($this->admin)
            ->putJson("/admin/users/{$target->id}", [
                'name' => $target->name,
                'email' => 'existing@test.com',
                'role' => $target->role,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
