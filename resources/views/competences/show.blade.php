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
            <span class="text-gray-900 font-medium truncate">{{ $competence->title }}</span>
        </nav>

        {{-- ============================================ --}}
        {{-- Блок 1: Главный блок компетенции            --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $competence->title }}</h1>

                    {{-- Кнопки управления (admin/expert) --}}
                    @can('competence-manage')
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button onclick="openEditCompetenceModal()"
                                class="inline-flex items-center px-3 py-1.5 bg-amber-50 border border-amber-300 rounded-md text-sm font-medium text-amber-700 hover:bg-amber-100 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Изменить
                            </button>
                            <form method="POST" action="{{ route('competences.destroy', $competence) }}"
                                onsubmit="return confirm('Удалить компетенцию «{{ $competence->title }}»? Это действие необратимо.')">
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

                <div class="prose max-w-none text-gray-700">
                    <p class="text-base sm:text-lg leading-relaxed">{{ $competence->description }}</p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- Блок 2: Модули компетенции                   --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Модули</h2>

                    @can('module-manage')
                        <button onclick="document.getElementById('createModuleModal').classList.remove('hidden'); document.getElementById('createModuleModal').classList.add('flex')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-500 active:bg-indigo-700 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Создать модуль
                        </button>
                    @endcan
                </div>

                @if($competence->modules->isEmpty())
                    <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-3 text-gray-500">Модули по этой компетенции пока не добавлены.</p>
                        @can('module-manage')
                            <p class="mt-1 text-gray-400 text-sm">Нажмите «Создать модуль», чтобы добавить первый.</p>
                        @endcan
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($competence->modules as $module)
                            <div class="group flex flex-col p-5 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-200">
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                    {{ $module->title }}
                                </h3>
                                @if($module->description)
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-3 flex-grow">
                                        {{ $module->description }}
                                    </p>
                                @endif
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="text-xs text-gray-400">
                                        {{ $module->submissions_count }} {{ trans_choice('задание|задания|заданий', $module->submissions_count) }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        @can('module-manage')
                                            <button onclick="openEditModuleModal('{{ $module->slug }}', '{{ addslashes($module->title) }}', '{{ addslashes($module->description ?? '') }}', '{{ addslashes($module->content) }}', {{ $module->order }})"
                                                class="text-xs text-amber-600 hover:text-amber-800 font-medium">
                                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        @endcan
                                        <a href="{{ route('modules.show', $module) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 transition shadow-sm">
                                            Перейти к модулю →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- Блок 3: Тесты (в разработке)                 --}}
        {{-- ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 sm:p-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6">Тесты</h2>
                <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                    <svg class="mx-auto h-16 w-16 text-gray-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <p class="mt-4 text-gray-500 text-lg font-medium">В разработке</p>
                    <p class="mt-1 text-gray-400 text-sm">Этот раздел скоро будет доступен.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- Модальное окно: создание модуля              --}}
{{-- ============================================ --}}
@can('module-manage')
<div id="createModuleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 overflow-y-auto"
    onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex')}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6 my-8" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Новый модуль</h2>
            <button onclick="document.getElementById('createModuleModal').classList.add('hidden'); document.getElementById('createModuleModal').classList.remove('flex')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('modules.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="competence_id" value="{{ $competence->id }}">

            <div>
                <label for="module-title" class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                <input type="text" name="title" id="module-title" required maxlength="255"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Например: Введение в моушн-дизайн">
            </div>
            <div>
                <label for="module-description" class="block text-sm font-medium text-gray-700 mb-1">Краткое описание</label>
                <textarea name="description" id="module-description" rows="2" maxlength="1000"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Краткое описание для карточки модуля..."></textarea>
            </div>
            <div>
                <label for="module-content" class="block text-sm font-medium text-gray-700 mb-1">Задание (контент) *</label>
                <textarea name="content" id="module-content" required rows="6"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                    placeholder="HTML-содержимое модуля: описание задания, условия, материалы..."></textarea>
            </div>
            <div>
                <label for="module-order" class="block text-sm font-medium text-gray-700 mb-1">Порядок</label>
                <input type="number" name="order" id="module-order" min="0" value="{{ $competence->modules->count() }}"
                    class="block w-24 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('createModuleModal').classList.add('hidden'); document.getElementById('createModuleModal').classList.remove('flex')"
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

{{-- Модальное окно: редактирование модуля --}}
<div id="editModuleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 overflow-y-auto"
    onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex')}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6 my-8" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Редактировать модуль</h2>
            <button onclick="document.getElementById('editModuleModal').classList.add('hidden'); document.getElementById('editModuleModal').classList.remove('flex')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form id="editModuleForm" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label for="edit-module-title" class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                <input type="text" name="title" id="edit-module-title" required maxlength="255"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="edit-module-description" class="block text-sm font-medium text-gray-700 mb-1">Краткое описание</label>
                <textarea name="description" id="edit-module-description" rows="2" maxlength="1000"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label for="edit-module-content" class="block text-sm font-medium text-gray-700 mb-1">Задание (контент) *</label>
                <textarea name="content" id="edit-module-content" required rows="6"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"></textarea>
            </div>
            <div>
                <label for="edit-module-order" class="block text-sm font-medium text-gray-700 mb-1">Порядок</label>
                <input type="number" name="order" id="edit-module-order" min="0"
                    class="block w-24 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editModuleModal').classList.add('hidden'); document.getElementById('editModuleModal').classList.remove('flex')"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm font-medium">
                    Отмена
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 transition text-sm font-semibold">
                    Сохранить
                </button>
            </div>
        </form>

        {{-- Кнопка удаления модуля --}}
        <form id="deleteModuleForm" method="POST" class="mt-3 pt-3 border-t border-gray-200"
            onsubmit="return confirm('Удалить этот модуль? Действие необратимо.')">
            @csrf @method('DELETE')
            <button type="submit"
                class="w-full px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-md hover:bg-red-100 transition text-sm font-medium">
                Удалить модуль
            </button>
        </form>
    </div>
</div>
@endcan

{{-- Модальное окно: редактирование компетенции --}}
@can('competence-manage')
<div id="editCompetenceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
    onclick="if(event.target===this){this.classList.add('hidden');this.classList.remove('flex')}">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Редактировать компетенцию</h2>
            <button onclick="document.getElementById('editCompetenceModal').classList.add('hidden'); document.getElementById('editCompetenceModal').classList.remove('flex')"
                class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form id="editCompetenceForm" method="POST" action="{{ route('competences.update', $competence) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label for="edit-competence-title" class="block text-sm font-medium text-gray-700 mb-1">Название *</label>
                <input type="text" name="title" id="edit-competence-title" required maxlength="255"
                    value="{{ $competence->title }}"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="edit-competence-description" class="block text-sm font-medium text-gray-700 mb-1">Описание *</label>
                <textarea name="description" id="edit-competence-description" required rows="4" maxlength="5000"
                    class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $competence->description }}</textarea>
            </div>
            <div>
                <label for="edit-competence-image" class="block text-sm font-medium text-gray-700 mb-1">Изображение</label>
                <input type="file" name="image" id="edit-competence-image" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editCompetenceModal').classList.add('hidden'); document.getElementById('editCompetenceModal').classList.remove('flex')"
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
    // Открытие модального окна редактирования компетенции
    function openEditCompetenceModal() {
        const modal = document.getElementById('editCompetenceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Открытие модального окна редактирования модуля
    function openEditModuleModal(slug, title, description, content, order) {
        const modal = document.getElementById('editModuleModal');
        const form = document.getElementById('editModuleForm');
        const deleteForm = document.getElementById('deleteModuleForm');

        form.action = '/modules/' + slug;
        deleteForm.action = '/modules/' + slug;

        document.getElementById('edit-module-title').value = title;
        document.getElementById('edit-module-description').value = description || '';
        document.getElementById('edit-module-content').value = content || '';
        document.getElementById('edit-module-order').value = order || 0;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Закрытие модальных окон по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id$="Modal"]').forEach(function(modal) {
                if (!modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        }
    });
</script>
@endpush
@endsection
