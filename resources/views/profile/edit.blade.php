<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Профиль') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="profileTabs()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash-уведомления --}}
            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Данные профиля успешно сохранены.') }}
                    </div>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Пароль успешно изменён.') }}
                    </div>
                </div>
            @endif

            @if (session('verification-link-sent'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                    class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ __('Письмо с подтверждением отправлено на ваш новый email.') }}
                    </div>
                </div>
            @endif

            {{-- Вкладки --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                        <button @click="activeTab = 'info'"
                            :class="activeTab === 'info'
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                            <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('Личные данные') }}
                        </button>

                        <button @click="activeTab = 'security'"
                            :class="activeTab === 'security'
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                            <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ __('Безопасность') }}
                        </button>

                        @if($user->isAdmin())
                            <button @click="activeTab = 'users'"
                                :class="activeTab === 'users'
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                                <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ __('Управление пользователями') }}
                            </button>
                        @endif
                    </nav>
                </div>

                {{-- Содержимое вкладок --}}
                <div class="p-6 sm:p-8">
                    {{-- Вкладка: Личные данные --}}
                    <div x-show="activeTab === 'info'" x-cloak>
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    {{-- Вкладка: Безопасность --}}
                    <div x-show="activeTab === 'security'" x-cloak>
                        @include('profile.partials.update-password-form')
                        <hr class="my-8 border-gray-200">
                        @include('profile.partials.delete-user-form')
                    </div>

                    {{-- Вкладка: Управление пользователями (только для админа) --}}
                    @if($user->isAdmin())
                        <div x-show="activeTab === 'users'" x-cloak id="users">
                            @include('profile.admin.users-table')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Скрипт управления вкладками --}}
    @push('scripts')
    <script>
        function profileTabs() {
            return {
                activeTab: '{{ old('tab', request()->has('tab') ? request()->input('tab') : (request()->getRequestUri() === route('profile.edit') . '#users' ? 'users' : 'info')) }}',

                init() {
                    // Обработка хеш-навигации
                    const hash = window.location.hash;
                    if (hash === '#users' && {{ $user->isAdmin() ? 'true' : 'false' }}) {
                        this.activeTab = 'users';
                    } else if (hash === '#security') {
                        this.activeTab = 'security';
                    }

                    window.addEventListener('hashchange', () => {
                        const h = window.location.hash;
                        if (h === '#users' && {{ $user->isAdmin() ? 'true' : 'false' }}) {
                            this.activeTab = 'users';
                        } else if (h === '#security') {
                            this.activeTab = 'security';
                        } else if (h === '#info') {
                            this.activeTab = 'info';
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
