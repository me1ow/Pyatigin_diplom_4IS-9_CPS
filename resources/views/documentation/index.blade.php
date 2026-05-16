@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Flash-сообщения --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-md shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Форма загрузки (admin + expert) --}}
        @if($canManage)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-semibold mb-4">{{ __('Загрузить новый документ') }}</h2>
                    <form method="POST" action="{{ route('documentation.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="title" :value="__('Название')" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                    :value="old('title')" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="section" :value="__('Раздел')" />
                                <select id="section" name="section"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Выберите раздел...') }}</option>
                                    <option value="bank" @selected(old('section') === 'bank')>{{ __('Банк заданий') }}</option>
                                    <option value="regulations" @selected(old('section') === 'regulations')>{{ __('Положения') }}</option>
                                    <option value="schedules" @selected(old('section') === 'schedules')>{{ __('Расписания') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('section')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="file" :value="__('Файл (PDF, DOCX, XLSX, до 50 МБ)')" />
                                <input id="file" name="file" type="file" accept=".pdf,.docx,.xlsx"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    required />
                                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                                {{ __('Загрузить') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Список документов --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-8">{{ __('Документы') }}</h1>

                @foreach($sections as $sectionKey => $sectionName)
                    <div class="mb-10">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                            {{ $sectionName }}
                        </h2>

                        @if(isset($documents[$sectionKey]) && $documents[$sectionKey]->count())
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($documents[$sectionKey] as $doc)
                                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">

                                        {{-- Иконка типа файла --}}
                                        <div class="shrink-0 mt-0.5">
                                            @if($doc->isPdf())
                                                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    <text x="10" y="18" font-size="6" font-weight="bold" fill="white" text-anchor="middle">PDF</text>
                                                </svg>
                                            @elseif($doc->extension() === 'docx')
                                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    <text x="9.5" y="18" font-size="5.5" font-weight="bold" fill="white" text-anchor="middle">DOC</text>
                                                </svg>
                                            @elseif($doc->extension() === 'xlsx' || $doc->extension() === 'xls')
                                                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                                    <text x="9.5" y="18" font-size="5.5" font-weight="bold" fill="white" text-anchor="middle">XLS</text>
                                                </svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Информация о файле --}}
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate" title="{{ $doc->title }}">
                                                {{ $doc->title }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $doc->filename }} &middot; {{ $doc->humanSize() }}
                                            </p>

                                            {{-- Кнопки действий --}}
                                            <div class="flex items-center gap-3 mt-2">
                                                <a href="{{ route('documentation.download', $doc) }}"
                                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                                                    {{ __('Скачать') }}
                                                </a>

                                                {{-- PDF: открыть inline --}}
                                                @if($doc->isPdf())
                                                    <a href="{{ route('documentation.view', $doc) }}"
                                                       target="_blank"
                                                       class="text-xs font-medium text-gray-500 hover:text-gray-700 transition">
                                                        {{ __('Открыть') }}
                                                    </a>
                                                @endif

                                                {{-- DOCX/XLSX: предпросмотр через Office Web Viewer --}}
                                                @if($doc->isOfficeDocument())
                                                    <a href="{{ route('documentation.preview', $doc) }}"
                                                       target="_blank"
                                                       class="text-xs font-medium text-gray-500 hover:text-gray-700 transition">
                                                        {{ __('Посмотреть') }}
                                                    </a>
                                                @endif

                                                {{-- Удаление (admin + expert) --}}
                                                @if($canManage)
                                                    <form method="POST" action="{{ route('documentation.destroy', $doc) }}"
                                                        onsubmit="return confirm('{{ __('Удалить документ «') . $doc->title . __('»? Это действие нельзя отменить.') }}')"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                                                            {{ __('Удалить') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">{{ __('В этом разделе пока нет документов.') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
