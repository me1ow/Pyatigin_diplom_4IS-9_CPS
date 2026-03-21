@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold">{{ $course->title }}</h1>

                <div class="mt-6">
                    <!-- Вкладки -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @foreach($course->modules as $index => $module)
                                <button onclick="showTab({{ $index }})"
                                        class="tab-button {{ $index == 0 ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ $module->title }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Контент модулей -->
                    @foreach($course->modules as $index => $module)
                        <div id="tab-{{ $index }}" class="tab-content mt-6" style="display: {{ $index == 0 ? 'block' : 'none' }}">
                            <div class="prose max-w-none">
                                {!! $module->content !!}
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('module.show', $module) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    Перейти к модулю →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(index) {
    // Скрыть все вкладки
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    // Показать выбранную
    document.getElementById('tab-' + index).style.display = 'block';
    // Изменить стили кнопок
    document.querySelectorAll('.tab-button').forEach((btn, i) => {
        if (i === index) {
            btn.classList.add('border-indigo-500', 'text-indigo-600');
            btn.classList.remove('border-transparent', 'text-gray-500');
        } else {
            btn.classList.remove('border-indigo-500', 'text-indigo-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        }
    });
}
</script>
@endsection