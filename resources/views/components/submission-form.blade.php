@props(['module', 'userSubmission' => null])

@auth
    @if($userSubmission)
        {{-- Блок: задание уже отправлено --}}
        <div class="mt-8 p-4 sm:p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Ваше задание</h3>
            <div class="space-y-2 text-sm">
                <p>
                    <strong>Статус:</strong>
                    @if($userSubmission->status == 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">На проверке</span>
                    @elseif($userSubmission->status == 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Принято</span>
                    @elseif($userSubmission->status == 'revision')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">На доработке</span>
                    @endif
                </p>
                <p><strong>Файл:</strong>
                    <a href="{{ route('submissions.download', $userSubmission) }}" class="text-indigo-600 hover:text-indigo-800 underline">
                        Скачать
                    </a>
                </p>
                @if($userSubmission->grade !== null)
                    <p><strong>Оценка:</strong> {{ number_format($userSubmission->grade, 2, ',', '') }}</p>
                @endif
                @if($userSubmission->feedback)
                    <p><strong>Комментарий эксперта:</strong> {{ $userSubmission->feedback }}</p>
                @endif
                @if($userSubmission->comment)
                    <p><strong>Ваш комментарий:</strong> {{ $userSubmission->comment }}</p>
                @endif
            </div>
        </div>
    @else
        {{-- Блок: форма отправки задания --}}
        <div class="mt-8 p-4 sm:p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Сдать задание</h3>
            <form method="POST" action="{{ route('submissions.store', $module) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="submission-file" class="block text-sm font-medium text-gray-700 mb-1">
                        Файл (ZIP, MP4, MOV, AVI) до 200 МБ
                    </label>
                    <input
                        type="file"
                        name="file"
                        id="submission-file"
                        required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('file')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="submission-comment" class="block text-sm font-medium text-gray-700 mb-1">
                        Комментарий (необязательно)
                    </label>
                    <textarea
                        name="comment"
                        id="submission-comment"
                        rows="3"
                        class="block w-full border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Опишите, что вы сделали, какие технологии использовали..."
                    ></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Отправить
                    </button>
                    <span class="text-xs text-gray-500">Максимальный размер: 200 МБ</span>
                </div>
            </form>
        </div>
    @endif
@else
    {{-- Блок: требуется авторизация --}}
    <div class="mt-8 p-4 sm:p-6 bg-gray-50 rounded-lg border border-gray-200 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
        </svg>
        <p class="mt-3 text-gray-600">
            Чтобы сдать задание,
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium underline">войдите</a>
            или
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium underline">зарегистрируйтесь</a>.
        </p>
    </div>
@endauth
