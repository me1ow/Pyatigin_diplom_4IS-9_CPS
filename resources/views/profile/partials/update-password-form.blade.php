<section x-data="passwordForm()">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Смена пароля') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Используйте надёжный пароль, который вы не используете на других сайтах.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6" @submit.prevent="submitForm">
        @csrf
        @method('put')

        {{-- Текущий пароль --}}
        <div>
            <x-input-label for="current_password" :value="__('Текущий пароль')" />
            <x-text-input
                id="current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                x-model="form.current_password"
                @@input="validateField('current_password')"
                x-bind:class="errors.current_password ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            <template x-if="errors.current_password">
                <p class="mt-1 text-sm text-red-600" x-text="errors.current_password"></p>
            </template>
        </div>

        {{-- Новый пароль --}}
        <div>
            <x-input-label for="password" :value="__('Новый пароль')" />
            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                x-model="form.password"
                @@input="validateField('password')"
                x-bind:class="errors.password ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <template x-if="errors.password">
                <p class="mt-1 text-sm text-red-600" x-text="errors.password"></p>
            </template>
            {{-- Индикатор сложности пароля --}}
            <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden" x-show="form.password.length > 0">
                <div class="h-full transition-all duration-300 rounded-full"
                    :class="{
                        'w-1/4 bg-red-500': passwordStrength === 'weak',
                        'w-2/4 bg-orange-500': passwordStrength === 'fair',
                        'w-3/4 bg-yellow-500': passwordStrength === 'good',
                        'w-full bg-green-500': passwordStrength === 'strong'
                    }">
                </div>
            </div>
        </div>

        {{-- Подтверждение пароля --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Подтверждение пароля')" />
            <x-text-input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                x-model="form.password_confirmation"
                @@input="validateField('password_confirmation')"
                x-bind:class="errors.password_confirmation ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            <template x-if="errors.password_confirmation">
                <p class="mt-1 text-sm text-red-600" x-text="errors.password_confirmation"></p>
            </template>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button x-bind:disabled="!isFormValid || isSubmitting">
                <span x-show="!isSubmitting">{{ __('Сменить пароль') }}</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Сохранение...') }}
                </span>
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 font-medium"
                >{{ __('Пароль изменён!') }}</p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
    function passwordForm() {
        return {
            form: {
                current_password: '',
                password: '',
                password_confirmation: '',
            },
            errors: {},
            isSubmitting: false,

            get passwordStrength() {
                const p = this.form.password;
                if (!p) return '';
                let score = 0;
                if (p.length >= 8) score++;
                if (p.match(/[a-z]/) && p.match(/[A-Z]/)) score++;
                if (p.match(/\d/)) score++;
                if (p.match(/[^a-zA-Z\d]/)) score++;
                return ['', 'weak', 'fair', 'good', 'strong'][score];
            },

            get isFormValid() {
                return this.form.current_password.length > 0
                    && this.form.password.length >= 8
                    && this.form.password === this.form.password_confirmation
                    && Object.keys(this.errors).length === 0;
            },

            validateField(field) {
                this.errors = { ...this.errors };

                if (field === 'current_password' || field === 'all') {
                    if (!this.form.current_password) {
                        this.errors.current_password = 'Введите текущий пароль.';
                    } else {
                        delete this.errors.current_password;
                    }
                }

                if (field === 'password' || field === 'all') {
                    if (!this.form.password) {
                        this.errors.password = 'Введите новый пароль.';
                    } else if (this.form.password.length < 8) {
                        this.errors.password = 'Пароль должен содержать минимум 8 символов.';
                    } else {
                        delete this.errors.password;
                    }
                }

                if (field === 'password_confirmation' || field === 'all') {
                    if (!this.form.password_confirmation) {
                        this.errors.password_confirmation = 'Подтвердите новый пароль.';
                    } else if (this.form.password_confirmation !== this.form.password) {
                        this.errors.password_confirmation = 'Пароли не совпадают.';
                    } else {
                        delete this.errors.password_confirmation;
                    }
                }
            },

            submitForm() {
                this.validateField('all');
                if (!this.isFormValid || this.isSubmitting) return;
                this.isSubmitting = true;
                this.$el.submit();
            }
        };
    }
</script>
@endpush
