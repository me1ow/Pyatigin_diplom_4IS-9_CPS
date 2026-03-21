@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <nav class="mb-4 text-sm text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-gray-700">Главная</a> &gt;
                    <a href="{{ route('competence.show', $module->course->competence->slug) }}" class="hover:text-gray-700">{{ $module->course->competence->title }}</a> &gt;
                    <a href="{{ route('course.show', $module->course) }}" class="hover:text-gray-700">{{ $module->course->title }}</a> &gt;
                    <span class="text-gray-700">{{ $module->title }}</span>
                </nav>

                <h1 class="text-2xl font-bold">{{ $module->title }}</h1>

                <div class="mt-6 prose max-w-none">
                    {!! $module->content !!}
                </div>

                @auth
                    @php
                        $userSubmission = $module->submissions()->where('user_id', Auth::id())->first();
                    @endphp

                    @if($userSubmission)
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-medium">Ваше задание</h3>
                            <p><strong>Статус:</strong>
                                @if($userSubmission->status == 'pending')
                                    <span class="text-yellow-600">На проверке</span>
                                @elseif($userSubmission->status == 'approved')
                                    <span class="text-green-600">Принято</span>
                                @elseif($userSubmission->status == 'revision')
                                    <span class="text-red-600">На доработке</span>
                                @endif
                            </p>
                            <p><strong>Файл:</strong> <a href="{{ $userSubmission->file_url }}" target="_blank" class="text-indigo-600">Скачать</a></p>
                            @if($userSubmission->feedback)
                                <p><strong>Комментарий эксперта:</strong> {{ $userSubmission->feedback }}</p>
                            @endif
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-medium">Сдать задание</h3>
                            <form method="POST" action="{{ route('submission.store', $module) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Файл (ZIP, MP4, MOV, AVI) до 200 МБ</label>
                                    <input type="file" name="file" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                                    @error('file')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Комментарий (необязательно)</label>
                                    <textarea name="comment" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"></textarea>
                                </div>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-500">Отправить</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg text-center">
                        <p>Чтобы сдать задание, <a href="{{ route('login') }}" class="text-indigo-600">войдите</a> или <a href="{{ route('register') }}" class="text-indigo-600">зарегистрируйтесь</a>.</p>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection