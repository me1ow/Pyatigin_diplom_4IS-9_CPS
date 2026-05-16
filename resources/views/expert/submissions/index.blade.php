@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold">{{ __('Проверка заданий') }}</h1>

                    {{-- Фильтр по статусу --}}
                    <form method="GET" class="flex items-center gap-2">
                        <select name="status" class="rounded-md border-gray-300 text-sm">
                            <option value="">{{ __('Все статусы') }}</option>
                            <option value="pending" @selected(request('status') === 'pending')>{{ __('На проверке') }}</option>
                            <option value="approved" @selected(request('status') === 'approved')>{{ __('Принято') }}</option>
                            <option value="revision" @selected(request('status') === 'revision')>{{ __('На доработке') }}</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                            {{ __('Фильтр') }}
                        </button>
                    </form>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if($submissions->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Студент') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Компетенция') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Модуль') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Статус') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Дата') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Файл') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Оценка') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Комментарий студента') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Комментарий эксперта') }}</th>
                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submissions as $submission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm">
                                            <div class="font-medium text-gray-900">{{ $submission->user->name }}</div>
                                            <div class="text-gray-500">{{ $submission->user->email }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm">{{ $submission->module->competence?->title ?? '—' }}</td>
                                        <td class="px-4 py-4 text-sm">{{ $submission->module->title }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            @if($submission->status == 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('На проверке') }}</span>
                                            @elseif($submission->status == 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Принято') }}</span>
                                            @elseif($submission->status == 'revision')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('На доработке') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $submission->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <a href="{{ route('expert.submissions.download', $submission) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Скачать') }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            {{ $submission->grade !== null ? number_format($submission->grade, 2, ',', '') : '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $submission->comment ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $submission->feedback ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <button type="button"
                                                onclick="openReviewModal({{ $submission->id }}, '{{ $submission->status }}', '{{ addslashes($submission->feedback ?? '') }}', {{ $submission->grade !== null ? (float)$submission->grade : 'null' }})"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ __('Оценить') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $submissions->links() }}
                    </div>
                @else
                    <p class="text-gray-500">{{ __('Нет заданий для проверки.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Модальное окно для оценки --}}
<div id="reviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Оценить задание') }}</h2>
        <form id="reviewForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Статус') }}</label>
                <select name="status" id="reviewStatus" class="w-full rounded-md border-gray-300">
                    <option value="pending">{{ __('На проверке') }}</option>
                    <option value="approved">{{ __('Принято') }}</option>
                    <option value="revision">{{ __('На доработке') }}</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Оценка') }}</label>
                <input type="number" name="grade" id="reviewGrade" step="0.01" min="0" max="999.99"
                    class="w-full rounded-md border-gray-300" placeholder="{{ __('Например: 5.0 или 4.5') }}">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Отзыв') }}</label>
                <textarea name="feedback" id="reviewFeedback" rows="3" class="w-full rounded-md border-gray-300" placeholder="{{ __('Напишите комментарий...') }}"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeReviewModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                    {{ __('Отмена') }}
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    {{ __('Сохранить') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openReviewModal(id, status, feedback, grade) {
        const form = document.getElementById('reviewForm');
        form.action = '/expert/submissions/' + id;
        document.getElementById('reviewStatus').value = status;
        document.getElementById('reviewFeedback').value = feedback || '';
        document.getElementById('reviewGrade').value = grade !== null ? grade : '';
        document.getElementById('reviewModal').classList.remove('hidden');
        document.getElementById('reviewModal').classList.add('flex');
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('reviewModal').classList.remove('flex');
    }

    // Закрытие по клику на оверлей
    document.getElementById('reviewModal').addEventListener('click', function(e) {
        if (e.target === this) closeReviewModal();
    });
</script>
@endpush
@endsection
