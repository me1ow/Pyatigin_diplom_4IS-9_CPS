<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertSubmissionController extends Controller
{
    /**
     * Список всех заданий на проверку (для экспертов и администраторов).
     */
    public function index(Request $request)
    {
        $query = Submission::with(['user', 'module.competence'])->latest();

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $submissions = $query->paginate(20);

        return view('expert.submissions.index', compact('submissions'));
    }

    /**
     * Обновление статуса / добавление отзыва.
     */
    public function update(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'status'   => 'required|in:pending,approved,revision',
            'feedback' => 'nullable|string|max:2000',
            'grade'    => 'nullable|numeric|min:0|max:999.99',
        ]);

        $submission->update([
            'status'   => $validated['status'],
            'feedback' => $validated['feedback'] ?? null,
            'grade'    => $validated['grade'] ?? null,
        ]);

        return redirect()
            ->route('expert.submissions.index')
            ->with('success', 'Статус задания обновлён.');
    }
}
