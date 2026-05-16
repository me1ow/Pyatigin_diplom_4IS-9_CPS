@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ============================================
             Раздел 1: «Движение «Профессионалы» — это»
             ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Движение «Профессионалы» — это</h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Блок 1: 3 этапа соревнований --}}
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-3">
                            {{--<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-bold text-lg">1</span>--}}
                            <h3 class="ml-3 text-lg font-bold text-gray-800">3 этапа соревнований</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Пройди региональный и итоговый (межрегиональный) этап, прими участие в Финале. Докажи, что ты лучший!
                        </p>
                    </div>

                    {{-- Блок 2: 299 компетенций --}}
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-3">
                            {{--<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-bold text-lg">2</span>--}}
                            <h3 class="ml-3 text-lg font-bold text-gray-800">408 компетенций</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Производство и инженерные технологии, сельское хозяйство и аграрные технологии, строительство и строительные технологии и другие — принять участие сможет каждый профессионал.
                        </p>
                    </div>

                    {{-- Блок 3: > 10 000 предприятий-партнёров --}}
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-3">
                            {{--<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-bold text-lg">3</span>--}}
                            <h3 class="ml-3 text-lg font-bold text-gray-800">> 10 000 предприятий-партнёров</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Профессионалы разрабатывают конкурсные задания, оценивают работу участников и предоставляют начало карьерного пути.
                        </p>
                    </div>
                </div>

                {{-- Кнопка регистрации на Чемпионат --}}
                <div class="text-center">
                    <a href="https://esim.firpo.ru/" target="_blank" rel="noopener noreferrer"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-10 sm:px-12 py-5 bg-indigo-600 text-white text-xl font-semibold rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Регистрация на Чемпионат
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================
             Раздел 2: Компетенции
             ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6 text-gray-900">
                {{-- Заголовок-ссылка без визуальных признаков ссылки --}}
                <a href="{{ route('competences.index') }}"
                   class="text-2xl font-bold mb-6 block text-gray-900 no-underline hover:text-gray-900 focus:text-gray-900 [&:visited]:text-gray-900 [&:active]:text-gray-900">
                    Компетенции
                </a>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @forelse($competences as $competence)
                        <a href="{{ route('competences.show', $competence) }}"
                           class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition-colors">
                            <h3 class="mb-2 text-xl font-bold text-gray-900">{{ $competence->title }}</h3>
                            <p class="text-gray-700">{{ Str::limit($competence->description, 100) }}</p>
                        </a>
                    @empty
                        <p class="col-span-full text-gray-500 italic">Компетенции пока не добавлены.</p>
                    @endforelse
                </div>

                {{-- Кнопка «Смотреть ещё» во всю ширину --}}
                <div>
                    <a href="{{ route('competences.index') }}"
                       class="block w-full text-center px-6 py-3 bg-white border-2 border-indigo-600 text-indigo-600 font-semibold rounded-md hover:bg-indigo-50 transition-colors duration-200">
                        Смотреть ещё
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================
             Раздел 3: Документы
             ============================================ --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{-- Заголовок-ссылка без визуальных признаков ссылки --}}
                <a href="{{ route('documentation.index') }}"
                   class="text-2xl font-bold mb-8 block text-gray-900 no-underline hover:text-gray-900 focus:text-gray-900 [&:visited]:text-gray-900 [&:active]:text-gray-900">
                    Документы
                </a>

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
                                                    Скачать
                                                </a>

                                                {{-- PDF: открыть inline --}}
                                                @if($doc->isPdf())
                                                    <a href="{{ route('documentation.view', $doc) }}"
                                                       target="_blank"
                                                       class="text-xs font-medium text-gray-500 hover:text-gray-700 transition">
                                                        Открыть
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">В этом разделе пока нет документов.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
