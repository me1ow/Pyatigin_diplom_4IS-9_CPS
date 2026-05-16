@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Мои задания</h1>

                @if($submissions->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Курс</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Модуль</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Файл</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Оценка</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Комментарий студента</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Комментарий эксперта</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td class="px-6 py-4">{{ $submission->module->competence?->title ?? '—' }}</td>
                                        <td class="px-6 py-4">{{ $submission->module->title }}</td>
                                        <td class="px-6 py-4">
                                            @if($submission->status == 'pending')
                                                <span class="text-yellow-600">На проверке</span>
                                            @elseif($submission->status == 'approved')
                                                <span class="text-green-600">Принято</span>
                                            @elseif($submission->status == 'revision')
                                                <span class="text-red-600">На доработке</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $submission->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-6 py-4"><a href="{{ route('submissions.download', $submission) }}" class="text-indigo-600">Скачать</a></td>
                                        <td class="px-6 py-4">
                                            {{ $submission->grade !== null ? number_format($submission->grade, 2, ',', '') : '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $submission->comment ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $submission->feedback ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Вы ещё не сдали ни одного задания.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection