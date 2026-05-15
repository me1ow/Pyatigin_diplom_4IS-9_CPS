<section x-data="profileInfoForm()">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Данные профиля') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Обновите имя и email вашего аккаунта. При смене email потребуется повторное подтверждение.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" @submit.prevent="submitForm">
        @csrf
        @method('patch')

        {{-- Имя --}}
        <div>
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
                x-model="form.name"
                @@input="validateField('name')"
                x-bind:class="errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
            <template x-if="errors.name">
                <p class="mt-1 text-sm text-red-600" x-text="errors.name"></p>
            </template>
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
                x-model="form.email"
                @@input="validateField('email')"
                x-bind:class="errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <template x-if="errors.email">
                <p class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
            </template>

            {{-- Статус верификации --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm text-amber-800">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        {{ __('Ваш email не подтверждён.') }}

                        <button form="send-verification" class="underline text-sm text-amber-800 hover:text-amber-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 ml-1">
                            {{ __('Нажмите, чтобы отправить письмо повторно.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Письмо с подтверждением отправлено на ваш email.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Кнопка сохранения --}}
        <div class="flex items-center gap-4">
            <x-primary-button x-bind:disabled="!isFormValid || isSubmitting">
                <span x-show="!isSubmitting">{{ __('Сохранить') }}</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Сохранение...') }}
                </span>
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-medium"
                >{{ __('Сохранено!') }}</p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
    function profileInfoForm() {
        return {
            form: {
                name: '{{ old('name', Auth::user()->name) }}',
                email: '{{ old('email', Auth::user()->email) }}',
            },
            errors: {},
            isSubmitting: false,

            get isFormValid() {
                return this.form.name.trim().length >= 2
                    && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)
                    && Object.keys(this.errors).length === 0;
            },

            validateField(field) {
                this.errors = { ...this.errors };

                if (field === 'name' || field === 'all') {
                    if (!this.form.name || this.form.name.trim().length < 2) {
                        this.errors.name = 'Имя должно содержать минимум 2 символа.';
                    } else if (this.form.name.trim().length > 255) {
                        this.errors.name = 'Имя не должно превышать 255 символов.';
                    } else {
                        delete this.errors.name;
                    }
                }

                if (field === 'email' || field === 'all') {
                    if (!this.form.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                        this.errors.email = 'Введите корректный email адрес.';
                    } else {
                        delete this.errors.email;
                    }
                }
            },

            submitForm() {
                this.validateField('all');
                if (!this.isFormValid || this.isSubmitting) return;
                this.isSubmitting = true;
                this.$el.submit();
            },

            init() {
                this.validateField('all');
            }
        };
    }
</script>
@endpush
