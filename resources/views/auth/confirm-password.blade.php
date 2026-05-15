<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Это защищённая область. Пожалуйста, подтвердите ваш пароль для продолжения.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" autocomplete="off">
        @csrf

        {{-- Пароль --}}
        <div>
            <x-input-label for="password" :value="__('Пароль')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="off" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Подтвердить') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
