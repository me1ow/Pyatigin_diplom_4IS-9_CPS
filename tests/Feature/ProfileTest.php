<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-strong-password',
                'password_confirmation' => 'new-strong-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Проверяем, что пароль действительно изменился
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-strong-password', $user->fresh()->password));
    }

    public function test_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }

    public function test_profile_edit_shows_admin_users_tab_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->actingAs($admin)
            ->get('/profile');

        $response->assertOk();
        $response->assertSee('Управление пользователями');
    }

    public function test_profile_edit_hides_admin_users_tab_for_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertDontSee('Управление пользователями');
    }
}
