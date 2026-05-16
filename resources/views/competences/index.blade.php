@extends('layouts.app')

@section('content')
<div class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Заголовок и кнопка создания (для admin/expert) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Компетенции чемпионата</h1>
                <p class="mt-1 text-gray-500 text-sm sm:text-base">{{ $competences->count() }} {{ trans_choice('компетенция|компетенции|компетенций', $competences->count()) }}</p>
            </div>

            @can('competence-manage')
                <button onclick="document.getElementById('createCompetenceModal').classList.remove('hidden'); document.getElementById('createCompetenceModal').classList.add('flex');"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-500 active:bg-indigo-700 transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Создать компетенцию
                </button>
            @endcan
        </div>

        {{-- Сетка компетенций (адаптивная) --}}
        @if($competences->isEmpty())
            <div class="text-center py-16 bg-white rounded-lg shadow-sm border border-gray-200">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="mt-4 text-gray-500 text-lg">Компетенции пока не добавлены.</p>
                @can('competence-manage')
                    <p class="mt-2 text-gray-400 text-sm">Нажмите «Создать компетенцию», чтобы добавить первую.</p>
                @endcan
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($competences as $competence)
                    <a href="{{ route('competences.show', $competence) }}"
                        class="group block p-5 sm:p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200">
                        <div class="flex items-start justify-between">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                {{ $competence->title }}
                            </h3>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-3">
                            {{ $competence->description }}
                        </p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="inline-flex items-center text-xs text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ $competence->modules_count }} {{ trans_choice('модуль|модуля|модулей', $competence->modules_count) }}
                            </span>
                            <span class="text-indigo-600 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                                Подробнее →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Модальное окно: создание компетенции --}}
@can('competence-manage')
<div id="createCompetenceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex')}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Новая компетенция</h2>
            <button onclick="document.getElementById('createCompetenceModal').classList.add('hidden'); document.getElementById('createCompetenceModal').classList.remove('flex')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('competences.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="competence-title" class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                <input type="text" name="title" id="competence-title" required maxlength="255"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Например: Моушн-дизайн">
            </div>
            <div>
                <label for="competence-description" class="block text-sm font-medium text-gray-700 mb-1">Описание *</label>
                <textarea name="description" id="competence-description" required rows="4" maxlength="5000"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Краткое описание компетенции..."></textarea>
            </div>
            <div>
                <label for="competence-image" class="block text-sm font-medium text-gray-700 mb-1">Изображение</label>
                <input type="file" name="image" id="competence-image" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP до 4 МБ (опционально)</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('createCompetenceModal').classList.add('hidden'); document.getElementById('createCompetenceModal').classList.remove('flex')"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm font-medium">
                    Отмена
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 transition text-sm font-semibold">
                    Создать
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
    // Закрытие модального окна по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('createCompetenceModal');
            if (modal && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    });
</script>
@endpush
@endsection
