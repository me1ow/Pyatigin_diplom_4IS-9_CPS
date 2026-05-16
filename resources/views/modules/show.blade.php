@extends('layouts.app')

@section('content')
<div class="py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Хлебные крошки --}}
        <nav class="mb-6 text-sm text-gray-500 flex flex-wrap items-center gap-1">
            <a href="{{ route('home') }}" class="hover:text-gray-700 transition">Главная</a>
            <span class="mx-1">›</span>
            <a href="{{ route('competences.index') }}" class="hover:text-gray-700 transition">Компетенции</a>
            <span class="mx-1">›</span>
            <a href="{{ route('competences.show', $module->competence) }}" class="hover:text-gray-700 transition truncate max-w-[200px]">{{ $module->competence->title }}</a>
            <span class="mx-1">›</span>
            <span class="text-gray-900 font-medium truncate">{{ $module->title }}</span>
        </nav>

        {{-- ============================================ --}}
        {{-- Блок 1: Название модуля                      --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $module->title }}</h1>

                    {{-- Кнопки управления (admin/expert) --}}
                    @can('module-manage')
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button onclick="openEditModuleModal()"
                                class="inline-flex items-center px-3 py-1.5 bg-amber-50 border border-amber-300 rounded-md text-sm font-medium text-amber-700 hover:bg-amber-100 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Изменить
                            </button>
                            <form method="POST" action="{{ route('modules.destroy', $module) }}"
                                onsubmit="return confirm('Удалить модуль «{{ $module->title }}»? Это действие необратимо.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-100 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Удалить
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- Блок 2: Описание модуля                      --}}
        {{-- ============================================ --}}
        @if($module->description)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 sm:p-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Описание</h2>
                    <div class="prose max-w-none text-gray-700">
                        <p class="text-base leading-relaxed">{{ $module->description }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- Блок 3: Задание                              --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Задание</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! $module->content !!}
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- Блок 4: Отправка задания (переиспользуемый)  --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 sm:p-8">
                <x-submission-form :module="$module" :userSubmission="$userSubmission" />
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- Модальное окно: редактирование модуля        --}}
{{-- ============================================ --}}
@can('module-manage')
<div id="editModuleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 overflow-y-auto"
    onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex')}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6 my-8" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Редактировать модуль</h2>
            <button onclick="closeEditModuleModal()"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('modules.update', $module) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label for="edit-module-title" class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                <input type="text" name="title" id="edit-module-title" required maxlength="255"
                    value="{{ $module->title }}"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="edit-module-description" class="block text-sm font-medium text-gray-700 mb-1">Краткое описание</label>
                <textarea name="description" id="edit-module-description" rows="2" maxlength="1000"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $module->description }}</textarea>
            </div>
            <div>
                <label for="edit-module-content" class="block text-sm font-medium text-gray-700 mb-1">Задание (контент) *</label>
                <textarea name="content" id="edit-module-content" required rows="6"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ $module->content }}</textarea>
            </div>
            <div>
                <label for="edit-module-order" class="block text-sm font-medium text-gray-700 mb-1">Порядок</label>
                <input type="number" name="order" id="edit-module-order" min="0" value="{{ $module->order }}"
                    class="block w-24 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModuleModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm font-medium">
                    Отмена
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 transition text-sm font-semibold">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
    function openEditModuleModal() {
        const modal = document.getElementById('editModuleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModuleModal() {
        const modal = document.getElementById('editModuleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModuleModal();
        }
    });
</script>
@endpush
@endsection
