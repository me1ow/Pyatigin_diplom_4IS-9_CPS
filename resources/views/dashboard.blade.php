<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Приветственный блок --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Добро пожаловать, :name!', ['name' => Auth::user()->name]) }}</h3>
                    <p class="mt-2 text-gray-600">
                        {{ __('Ваша роль:') }}
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if(Auth::user()->isAdmin()) bg-red-100 text-red-800
                            @elseif(Auth::user()->isExpert()) bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ Auth::user()->role }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Рекомендации курсов --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('📚 Рекомендованные курсы') }}</h3>

                    @php
                        use App\Models\UserProgress;
                        use App\Models\Course;

                        $user = Auth::user();
                        $startedCourseIds = UserProgress::where('user_id', $user->id)
                            ->whereHas('lesson.module.course')
                            ->get()
                            ->pluck('lesson.module.course.id')
                            ->unique()
                            ->values();

                        $recommendedCourses = Course::with('competence')
                            ->when($startedCourseIds->isNotEmpty(), function ($query) use ($startedCourseIds) {
                                $query->whereNotIn('id', $startedCourseIds);
                            })
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($recommendedCourses->isEmpty())
                        <p class="text-gray-500 italic">
                            {{ __('Все доступные курсы уже начаты или курсы пока не добавлены.') }}
                        </p>
                        <a href="{{ route('competences.index') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            {{ __('Перейти к списку компетенций →') }}
                        </a>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($recommendedCourses as $course)
                                <a href="{{ route('courses.show', $course) }}"
                                    class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-400 hover:shadow-md transition">
                                    <h4 class="font-semibold text-gray-800">{{ $course->title }}</h4>
                                    @if($course->competence)
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ __('Компетенция: :name', ['name' => $course->competence->title]) }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-2">
                                        {{ __('Модулей: :count', ['count' => $course->modules->count() ?? 0]) }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Быстрые ссылки --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('⚡ Быстрые действия') }}</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('competences.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                            {{ __('Компетенции') }}
                        </a>
                        <a href="{{ route('submissions.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition">
                            {{ __('Мои задания') }}
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition">
                            {{ __('Профиль') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
