<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Отображение страницы профиля с вкладками.
     * Для администратора добавляются данные для управления пользователями.
     */
    public function edit(Request $request): View
    {
        $currentUser = $request->user();

        // Администратору передаём список всех пользователей для имперсонации
        $impersonatableUsers = $currentUser->isAdmin()
            ? User::where('id', '!=', $currentUser->id)->orderBy('name')->get()
            : collect();

        // Для вкладки «Управление пользователями» админу отдаём всех, кроме себя
        $allUsers = $currentUser->isAdmin()
            ? User::where('id', '!=', $currentUser->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('profile.edit', [
            'user' => $currentUser,
            'impersonatableUsers' => $impersonatableUsers,
            'allUsers' => $allUsers,
        ]);
    }

    /**
     * Обновление профиля (имя, email).
     * При смене email сбрасывается верификация и отправляется новое письмо.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email;

        $user->fill($request->validated());

        $emailChanged = $user->isDirty('email');

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Отправляем повторное письмо для верификации, если email изменился
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Смена пароля текущим пользователем.
     * Требует текущий пароль для подтверждения.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Удаление аккаунта текущим пользователем.
     * Требует подтверждения текущим паролем.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
