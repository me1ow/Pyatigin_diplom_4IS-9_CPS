<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Список пользователей с поиском и фильтрацией (JSON для AJAX).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Поиск по имени или email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Фильтрация по роли
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($users);
    }

    /**
     * Создание нового пользователя администратором.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'string', Rule::in(['user', 'expert', 'admin'])],
            'password' => ['required', Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now(); // Админ создаёт уже верифицированного пользователя

        $user = User::create($validated);

        return response()->json([
            'message' => 'Пользователь успешно создан.',
            'user' => $user,
        ], 201);
    }

    /**
     * Получение данных одного пользователя (для модального окна редактирования).
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Обновление пользователя администратором.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', 'string', Rule::in(['user', 'expert', 'admin'])],
            'password' => ['nullable', Password::defaults()],
        ]);

        // Если email изменился — сбрасываем верификацию
        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        // Если передан новый пароль — хешируем, иначе убираем из массива
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Пользователь успешно обновлён.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Удаление пользователя администратором.
     */
    public function destroy(User $user): JsonResponse
    {
        // Запрещаем удалять самого себя
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Нельзя удалить самого себя.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Пользователь успешно удалён.',
        ]);
    }
}
