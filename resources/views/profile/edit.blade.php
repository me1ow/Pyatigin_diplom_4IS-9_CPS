<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Профиль') }}
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
                        $user = Auth::user();
                        $startedCourseIds = \App\Models\UserProgress::where('user_id', $user->id)
                            ->whereHas('lesson.module.course')
                            ->get()
                            ->pluck('lesson.module.course.id')
                            ->unique()
                            ->values();

                        $recommendedCourses = \App\Models\Course::with('competence')
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

            {{-- Блок переключения ролей (только для администратора) --}}
            @if($user->isAdmin())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            🔄 {{ __('Управление ролями / Имперсонация') }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Выберите пользователя, чтобы войти под его учётной записью. Ваша административная сессия будет сохранена.') }}
                        </p>

                        @if($users->isEmpty())
                            <p class="text-sm text-gray-500 italic">{{ __('Нет других пользователей в системе.') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach($users as $targetUser)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $targetUser->name }}</span>
                                            <span class="ml-2 text-xs text-gray-500">({{ $targetUser->email }})</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($targetUser->role === 'admin') bg-red-100 text-red-800
                                                @elseif($targetUser->role === 'expert') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $targetUser->role }}
                                            </span>
                                        </div>
                                        <form method="POST" action="{{ route('impersonate', $targetUser) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('Войти как') }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
