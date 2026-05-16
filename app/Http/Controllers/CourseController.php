<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Просмотр конкретного курса.
     */
    public function show(Course $course)
    {
        $course->load(['competence', 'modules.lessons']);
        return view('courses.show', compact('course'));
    }

    /**
     * Рекомендации курсов для текущего пользователя.
     * Алгоритм:
     * 1. Получаем курсы, которые пользователь уже начал (через UserProgress → Lesson → Module → Course).
     * 2. Исключаем их из общего списка.
     * 3. Сортируем оставшиеся: сначала самые новые.
     * 4. Возвращаем до 5 курсов.
     */
    public function recommendations(Request $request)
    {
        $user = Auth::user();

        // ID курсов, с которыми пользователь уже взаимодействовал
        // Используем JOIN вместо ленивой загрузки для предотвращения N+1
        $startedCourseIds = \App\Models\UserProgress::where('user_id', $user->id)
            ->whereHas('lesson.module.course')
            ->join('lessons', 'user_progress.lesson_id', '=', 'lessons.id')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->select('modules.course_id')
            ->distinct()
            ->pluck('course_id');

        // Рекомендуем курсы, которые пользователь ещё не начинал
        $recommendedCourses = Course::with('competence')
            ->when($startedCourseIds->isNotEmpty(), function ($query) use ($startedCourseIds) {
                $query->whereNotIn('id', $startedCourseIds);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('courses.recommendations', compact('recommendedCourses'));
    }
}
