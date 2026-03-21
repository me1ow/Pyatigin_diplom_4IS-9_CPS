@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold">{{ $competence->title }}</h1>
                <p class="mt-2 text-gray-600">{{ $competence->description }}</p>

                <h2 class="text-xl font-semibold mt-8 mb-4">Курсы по компетенции</h2>
                <div class="space-y-4">
                    @foreach($competence->courses as $course)
                        <div class="border rounded-lg p-4">
                            <a href="{{ route('course.show', $course) }}" class="text-lg font-medium text-indigo-600 hover:underline">
                                {{ $course->title }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection