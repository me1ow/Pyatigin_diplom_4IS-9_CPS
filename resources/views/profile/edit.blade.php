<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Блок переключения ролей (только для администратора) --}}
            @if($user->isAdmin())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            🔄 {{ __('Управление ролями / Имперсонация') }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Выберите пользователя, чтобы войти под его учётной записью. Ваша административная сессия будет сохранена.') }}
                        </p>

                        @if($users->isEmpty())
                            <p class="text-sm text-gray-500 italic">{{ __('Нет других пользователей в системе.') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach($users as $targetUser)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $targetUser->name }}</span>
                                            <span class="ml-2 text-xs text-gray-500">({{ $targetUser->email }})</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($targetUser->role === 'admin') bg-red-100 text-red-800
                                                @elseif($targetUser->role === 'expert') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $targetUser->role }}
                                            </span>
                                        </div>
                                        <form method="POST" action="{{ route('impersonate', $targetUser) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('Войти как') }}
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
