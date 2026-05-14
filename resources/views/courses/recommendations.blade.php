<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('📚 Рекомендованные курсы') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($recommendedCourses->isEmpty())
                        <p class="text-gray-500">{{ __('Вы уже начали все доступные курсы или курсы пока не добавлены.') }}</p>
                        <a href="{{ route('competences.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ __('← Вернуться к списку компетенций') }}
                        </a>
                    @else
                        <p class="text-gray-600 mb-6">{{ __('Курсы, подобранные специально для вас на основе ваших интересов:') }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($recommendedCourses as $course)
                                <a href="{{ route('courses.show', $course) }}"
                                    class="block p-6 border border-gray-200 rounded-lg hover:border-indigo-400 hover:shadow-lg transition">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h3>
                                    @if($course->competence)
                                        <p class="text-sm text-indigo-600 mt-1">
                                            {{ $course->competence->title }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-3">
                                        {{ __('Добавлен: :date', ['date' => $course->created_at->format('d.m.Y')]) }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
