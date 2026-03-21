@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Компетенции чемпионата</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($competences as $competence)
                        <a href="{{ route('competence.show', $competence->slug) }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                            <h3 class="mb-2 text-xl font-bold">{{ $competence->title }}</h3>
                            <p class="text-gray-700">{{ Str::limit($competence->description, 100) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection