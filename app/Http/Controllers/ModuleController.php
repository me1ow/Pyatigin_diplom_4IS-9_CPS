<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ModuleController extends Controller
{
    /**
     * Страница конкретного модуля.
     */
    public function show(Module $module)
    {
        $module->load('competence');

        // Текущая отправка пользователя (если авторизован)
        $userSubmission = null;
        if (Auth::check()) {
            $userSubmission = $module->submissions()
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('modules.show', compact('module', 'userSubmission'));
    }

    /**
     * Создание нового модуля (admin/expert).
     */
    public function store(Request $request)
    {
        Gate::authorize('module-manage');

        $validated = $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'content'       => 'required|string|max:50000',
            'order'         => 'nullable|integer|min:0',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Module::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Module::create([
            'competence_id' => $validated['competence_id'],
            'title'         => $validated['title'],
            'slug'          => $slug,
            'description'   => $validated['description'] ?? null,
            'content'       => $validated['content'],
            'order'         => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Модуль успешно создан.');
    }

    /**
     * Обновление модуля (admin/expert).
     */
    public function update(Request $request, Module $module)
    {
        Gate::authorize('module-manage');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content'     => 'required|string|max:50000',
            'order'       => 'nullable|integer|min:0',
        ]);

        // Обновляем slug при смене title
        $slug = $module->slug;
        if ($validated['title'] !== $module->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Module::where('slug', $slug)->where('id', '!=', $module->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $module->update([
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? $module->description,
            'content'     => $validated['content'],
            'order'       => $validated['order'] ?? $module->order,
        ]);

        return redirect()
            ->route('modules.show', $module)
            ->with('success', 'Модуль обновлён.');
    }

    /**
     * Удаление модуля (admin/expert).
     */
    public function destroy(Module $module)
    {
        Gate::authorize('module-manage');

        $competenceSlug = $module->competence->slug;

        $module->delete();

        return redirect()
            ->route('competences.show', $competenceSlug)
            ->with('success', 'Модуль удалён.');
    }
}
