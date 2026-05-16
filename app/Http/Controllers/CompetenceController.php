<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class CompetenceController extends Controller
{
    /**
     * Каталог всех компетенций (плитки).
     */
    public function index()
    {
        $competences = Competence::withCount('modules')->orderBy('title')->get();
        return view('competences.index', compact('competences'));
    }

    /**
     * Детальная страница компетенции с модулями.
     */
    public function show(Competence $competence)
    {
        $competence->load(['modules' => function ($query) {
            $query->orderBy('order')->withCount('submissions');
        }]);

        return view('competences.show', compact('competence'));
    }

    /**
     * Создание новой компетенции (admin/expert).
     */
    public function store(Request $request)
    {
        Gate::authorize('competence-manage');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (Competence::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $competence = Competence::create([
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'],
            'image'       => null,
        ]);

        // Загрузка изображения
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('competences', 'public');
            $competence->update(['image' => $path]);
        }

        return redirect()
            ->route('competences.show', $competence)
            ->with('success', 'Компетенция успешно создана.');
    }

    /**
     * Обновление компетенции (admin/expert).
     */
    public function update(Request $request, Competence $competence)
    {
        Gate::authorize('competence-manage');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        // Обновляем slug только при смене title
        $slug = $competence->slug;
        if ($validated['title'] !== $competence->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            while (Competence::where('slug', $slug)->where('id', '!=', $competence->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $competence->update([
            'title'       => $validated['title'],
            'slug'        => $slug,
            'description' => $validated['description'],
        ]);

        // Загрузка изображения
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('competences', 'public');
            $competence->update(['image' => $path]);
        }

        return redirect()
            ->route('competences.show', $competence)
            ->with('success', 'Компетенция обновлена.');
    }

    /**
     * Удаление компетенции (admin/expert).
     */
    public function destroy(Competence $competence)
    {
        Gate::authorize('competence-manage');

        $competence->delete();

        return redirect()
            ->route('competences.index')
            ->with('success', 'Компетенция удалена.');
    }
}
