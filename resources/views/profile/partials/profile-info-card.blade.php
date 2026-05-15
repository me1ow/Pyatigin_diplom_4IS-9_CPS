<section x-data="profileInfoCard()">
    {{-- Карточка профиля --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Баннер-шапка --}}
        <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

        {{-- Тело карточки --}}
        <div class="px-6 pb-6">
            {{-- Аватар --}}
            <div class="flex justify-center sm:justify-start -mt-12 mb-4">
                <div class="w-24 h-24 rounded-full bg-white border-4 border-white shadow-lg flex items-center justify-center text-3xl font-bold text-indigo-600 bg-indigo-50">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                {{-- Основная информация --}}
                <div class="space-y-3">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->email }}

                            @if (!$user->hasVerifiedEmail())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 ml-2">
                                    {{ __('Не подтверждён') }}
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        {{-- Роль --}}
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($user->isAdmin()) bg-red-100 text-red-800
                            @elseif($user->isExpert()) bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            @if($user->isAdmin())
                                {{ __('Администратор') }}
                            @elseif($user->isExpert())
                                {{ __('Эксперт') }}
                            @else
                                {{ __('Ученик') }}
                            @endif
                        </span>

                        {{-- Дата регистрации --}}
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('Зарегистрирован') }}: {{ $user->created_at->isoFormat('D MMMM YYYY, HH:mm') }}
                        </span>

                        {{-- Обновлён --}}
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('Обновлён') }}: {{ $user->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                {{-- Кнопки действий --}}
                <div class="flex flex-wrap gap-2">
                    {{-- Редактировать профиль --}}
                    <button @@click="showEditModal = true"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Редактировать') }}
                    </button>

                    {{-- Выйти --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Выйти') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== МОДАЛЬНОЕ ОКНО: Редактирование профиля ========== --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="edit-profile-modal" role="dialog">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @@click="closeEditModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 z-10" @@click.outside="closeEditModal()">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900" id="edit-profile-modal">
                        {{ __('Редактировать профиль') }}
                    </h3>
                    <button @@click="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="post" action="{{ route('profile.update') }}" @@submit.prevent="submitEditForm" class="space-y-4">
                    @csrf
                    @method('patch')

                    {{-- Имя --}}
                    <div>
                        <x-input-label for="pi_name" :value="__('Имя')" />
                        <input id="pi_name" type="text" name="name" x-model="editForm.name"
                            required maxlength="255"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            :class="editErrors.name ? 'border-red-500' : ''">
                        <template x-if="editErrors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="editErrors.name"></p>
                        </template>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="pi_email" :value="__('Email')" />
                        <input id="pi_email" type="email" name="email" x-model="editForm.email"
                            required maxlength="255"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            :class="editErrors.email ? 'border-red-500' : ''">
                        <template x-if="editErrors.email">
                            <p class="mt-1 text-sm text-red-600" x-text="editErrors.email"></p>
                        </template>
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        @if (!$user->hasVerifiedEmail())
                            <div class="mt-2">
                                <button form="send-verification" type="submit"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                                    {{ __('Отправить письмо подтверждения повторно') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Кнопки --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @@click="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            {{ __('Отмена') }}
                        </button>
                        <button type="submit" :disabled="editSubmitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:opacity-60 transition">
                            <span x-show="!editSubmitting">{{ __('Сохранить') }}</span>
                            <span x-show="editSubmitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Сохранение...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function profileInfoCard() {
        return {
            showEditModal: false,
            editForm: {
                name: '{{ old('name', $user->name) }}',
                email: '{{ old('email', $user->email) }}',
            },
            editErrors: {},
            editSubmitting: false,

            closeEditModal() {
                this.showEditModal = false;
                this.editErrors = {};
                this.editSubmitting = false;
            },

            validateEditForm() {
                this.editErrors = {};
                if (!this.editForm.name || this.editForm.name.trim().length < 2) {
                    this.editErrors.name = 'Имя должно содержать минимум 2 символа.';
                }
                if (!this.editForm.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.editForm.email)) {
                    this.editErrors.email = 'Введите корректный email адрес.';
                }
                return Object.keys(this.editErrors).length === 0;
            },

            submitEditForm() {
                if (!this.validateEditForm()) return;
                this.editSubmitting = true;
                this.$el.submit();
            },
        };
    }
</script>
@endpush
