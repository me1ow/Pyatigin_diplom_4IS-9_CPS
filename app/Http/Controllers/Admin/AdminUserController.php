<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Сортировка
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'email', 'role', 'created_at'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $users = $query->orderBy($sort, $direction)->paginate(15);

        return response()->json($users);
    }

    /**
     * Блокировка / разблокировка пользователя (inline, AJAX).
     */
    public function toggleBlock(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Нельзя заблокировать самого себя.'], 422);
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        return response()->json([
            'message' => $user->is_blocked ? 'Пользователь заблокирован.' : 'Пользователь разблокирован.',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Быстрая смена роли пользователя (inline, AJAX).
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['user', 'expert', 'admin'])],
        ]);

        // Запрещаем менять роль самому себе
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Нельзя изменить свою роль.'], 422);
        }

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'Роль пользователя обновлена.',
            'user' => $user->fresh(),
        ]);
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
        if ($user->id === Auth::id()) {
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
