<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Забыли пароль? Укажите свой email, и мы отправим вам ссылку для сброса пароля.') }}
    </div>

    {{-- Статус сессии --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
        @csrf

        {{-- Адрес электронной почты --}}
        <div>
            <x-input-label for="email" :value="__('Электронная почта')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('← Назад ко входу') }}
            </a>

            <x-primary-button>
                {{ __('Отправить ссылку') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
