<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Имперсонация — вход под другим пользователем.
     * Доступно только администраторам (проверено middleware 'admin').
     * Использует implicit route model binding (User $user).
     */
    public function impersonate(Request $request, User $user)
    {
        // Middleware 'admin' уже гарантирует, что текущий пользователь — администратор,
        // поэтому дополнительная проверка isAdmin() избыточна.

        // Сохраняем ID оригинального администратора в сессии
        Session::put('impersonator_id', Auth::id());

        // Авторизуемся под целевым пользователем
        Auth::loginUsingId($user->id);

        return redirect()->route('dashboard')
            ->with('success', "Вы вошли как {$user->name} ({$user->role}).");
    }

    /**
     * Выход из режима имперсонации — возврат к администратору.
     */
    public function stopImpersonate(Request $request)
    {
        $impersonatorId = Session::get('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        // Возвращаемся к оригинальному администратору
        Auth::loginUsingId($impersonatorId);

        // Очищаем сессию имперсонации
        Session::forget('impersonator_id');

        return redirect()->route('dashboard')
            ->with('success', 'Вы вернулись в режим администратора.');
    }
}
