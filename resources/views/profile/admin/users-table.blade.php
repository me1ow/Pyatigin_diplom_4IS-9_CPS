<section x-data="adminUsers()" x-init="init()" id="users-section">
    {{-- Заголовок и кнопка «Добавить» --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h3 class="text-lg font-medium text-gray-900">
            {{ __('Список пользователей') }}
        </h3>
        <button @@click="openCreateModal()"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Добавить пользователя') }}
        </button>
    </div>

    {{-- Поиск и фильтрация --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text"
                x-model="search"
                @@input.debounce.300ms="fetchUsers()"
                placeholder="{{ __('Поиск по имени или email...') }}"
                class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
        <select x-model="roleFilter" @@change="fetchUsers()"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm min-w-[140px]">
            <option value="">{{ __('Все роли') }}</option>
            <option value="user">{{ __('Ученик') }}</option>
            <option value="expert">{{ __('Эксперт') }}</option>
            <option value="admin">{{ __('Администратор') }}</option>
        </select>
    </div>

    {{-- Индикатор загрузки --}}
    <div x-show="loading" class="flex justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Сообщение об ошибке --}}
    <div x-show="error" x-cloak class="p-4 mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm" x-text="error"></div>

    {{-- Flash-сообщение --}}
    <div x-show="flashMessage" x-cloak x-transition
        class="p-4 mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center"
        x-init="setTimeout(() => flashMessage = '', 4000)">
        <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span x-text="flashMessage"></span>
    </div>

    {{-- Таблица пользователей --}}
    <div x-show="!loading" class="overflow-x-auto">
        <div x-show="users.length === 0 && !loading" x-cloak class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Пользователи не найдены') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('Попробуйте изменить параметры поиска или фильтра.') }}</p>
        </div>

        <table x-show="users.length > 0" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Имя') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Роль') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Дата регистрации') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Действия') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="user in users" :key="user.id">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.id"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium"
                                    :class="roleBadgeClass(user.role)">
                                    <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="user.email"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="roleBadgeClass(user.role)"
                                x-text="roleLabel(user.role)">
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(user.created_at)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <button @@click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 transition">
                                {{ __('Ред.') }}
                            </button>
                            <button @@click="confirmDelete(user)" class="text-red-600 hover:text-red-900 transition">
                                {{ __('Удалить') }}
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Пагинация --}}
    <div x-show="pagination && pagination.last_page > 1" class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            {{ __('Показано') }} <span x-text="pagination.from || 0"></span>–<span x-text="pagination.to || 0"></span>
            {{ __('из') }} <span x-text="pagination.total"></span>
        </div>
        <div class="flex gap-1">
            <button @@click="goToPage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                class="px-3 py-1 text-sm border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                &laquo;
            </button>
            <template x-for="page in paginationLinks" :key="page">
                <button x-show="page !== '...'" @@click="goToPage(page)"
                    :class="page === pagination.current_page ? 'bg-indigo-600 text-white' : 'hover:bg-gray-50'"
                    class="px-3 py-1 text-sm border rounded-md transition" x-text="page"></button>
                <span x-show="page === '...'" class="px-2 py-1 text-sm text-gray-400">...</span>
            </template>
            <button @@click="goToPage(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                class="px-3 py-1 text-sm border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition">
                &raquo;
            </button>
        </div>
    </div>

    {{-- ========== МОДАЛЬНОЕ ОКНО: Создание / Редактирование ========== --}}
    <div x-show="showUserModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @@click="closeUserModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 z-10"
                @@click.outside="closeUserModal()">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                        <span x-text="modalTitle()"></span>
                    </h3>
                    <button @@click="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @@submit.prevent="submitUserForm()" class="space-y-4">
                    {{-- Имя --}}
                    <div>
                        <x-input-label for="mu_name" :value="__('Имя')" />
                        <input id="mu_name" type="text" x-model="userForm.name" required maxlength="255"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            :class="userFormErrors.name ? 'border-red-500' : ''">
                        <template x-if="userFormErrors.name">
                            <p class="mt-1 text-sm text-red-600" x-text="userFormErrors.name"></p>
                        </template>
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="mu_email" :value="__('Email')" />
                        <input id="mu_email" type="email" x-model="userForm.email" required maxlength="255"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            :class="userFormErrors.email ? 'border-red-500' : ''">
                        <template x-if="userFormErrors.email">
                            <p class="mt-1 text-sm text-red-600" x-text="userFormErrors.email"></p>
                        </template>
                    </div>

                    {{-- Роль --}}
                    <div>
                        <x-input-label for="mu_role" :value="__('Роль')" />
                        <select id="mu_role" x-model="userForm.role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="user">{{ __('Ученик') }}</option>
                            <option value="expert">{{ __('Эксперт') }}</option>
                            <option value="admin">{{ __('Администратор') }}</option>
                        </select>
                    </div>

                    {{-- Пароль --}}
                    <div>
                        <x-input-label for="mu_password" x-bind:value="passwordLabel()" />
                        <input id="mu_password" type="password" x-model="userForm.password"
                            x-bind:required="!editingUser" minlength="8"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            :class="userFormErrors.password ? 'border-red-500' : ''">
                        <template x-if="userFormErrors.password">
                            <p class="mt-1 text-sm text-red-600" x-text="userFormErrors.password"></p>
                        </template>
                    </div>

                    {{-- Кнопки --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @@click="closeUserModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                            {{ __('Отмена') }}
                        </button>
                        <button type="submit" :disabled="userFormSubmitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:opacity-60 transition">
                            <span x-show="!userFormSubmitting" x-text="submitButtonLabel()"></span>
                            <span x-show="userFormSubmitting" class="flex items-center">
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

    {{-- ========== МОДАЛЬНОЕ ОКНО: Подтверждение удаления ========== --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="delete-modal-title" role="dialog">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @@click="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 z-10">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="delete-modal-title">
                        {{ __('Удаление пользователя') }}
                    </h3>
                </div>

                <p class="text-sm text-gray-600 mb-2">
                    {{ __('Вы уверены, что хотите удалить этого пользователя?') }}
                </p>
                <p class="text-sm font-medium text-gray-900 mb-5" x-text="deletingUser ? deletingUser.name + ' (' + deletingUser.email + ')' : ''"></p>
                <p class="text-sm text-red-600 mb-5">
                    {{ __('Это действие необратимо. Все данные пользователя будут удалены.') }}
                </p>

                <div class="flex justify-end gap-3">
                    <button @@click="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">
                        {{ __('Отмена') }}
                    </button>
                    <button @@click="deleteUser()" :disabled="deleteSubmitting"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 disabled:opacity-60 transition">
                        <span x-show="!deleteSubmitting">{{ __('Удалить') }}</span>
                        <span x-show="deleteSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Удаление...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Глобальные JS-переменные для переводов и роутов --}}
<script>
    window.AppUsers = {
        labelEditUser: '{{ __('Редактировать пользователя') }}',
        labelNewUser: '{{ __('Новый пользователь') }}',
        labelPasswordEdit: '{{ __('Новый пароль (оставьте пустым, чтобы не менять)') }}',
        labelPasswordNew: '{{ __('Пароль') }}',
        labelSave: '{{ __('Сохранить') }}',
        labelCreate: '{{ __('Создать') }}',
        labelUser: '{{ __('Ученик') }}',
        labelExpert: '{{ __('Эксперт') }}',
        labelAdmin: '{{ __('Админ') }}',
        urlIndex: '{{ route('admin.users.index') }}',
        urlStore: '{{ route('admin.users.store') }}',
        urlUpdate: '{{ route('admin.users.update', ['user' => '__ID__']) }}',
        urlDestroy: '{{ route('admin.users.destroy', ['user' => '__ID__']) }}',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    };
</script>

@push('scripts')
@verbatim
<script>
    function adminUsers() {
        return {
            // ===== Состояние =====
            users: [],
            pagination: null,
            loading: true,
            error: '',
            flashMessage: '',
            search: '',
            roleFilter: '',

            showUserModal: false,
            editingUser: null,
            userForm: { name: '', email: '', role: 'user', password: '' },
            userFormErrors: {},
            userFormSubmitting: false,

            showDeleteModal: false,
            deletingUser: null,
            deleteSubmitting: false,

            init() { this.fetchUsers(); },

            // ===== Метки и тексты =====
            modalTitle() {
                return this.editingUser ? window.AppUsers.labelEditUser : window.AppUsers.labelNewUser;
            },
            passwordLabel() {
                return this.editingUser ? window.AppUsers.labelPasswordEdit : window.AppUsers.labelPasswordNew;
            },
            submitButtonLabel() {
                return this.editingUser ? window.AppUsers.labelSave : window.AppUsers.labelCreate;
            },
            roleLabel(role) {
                const map = { user: window.AppUsers.labelUser, expert: window.AppUsers.labelExpert, admin: window.AppUsers.labelAdmin };
                return map[role] || role;
            },
            roleBadgeClass(role) {
                const map = { user: 'bg-green-100 text-green-800', expert: 'bg-yellow-100 text-yellow-800', admin: 'bg-red-100 text-red-800' };
                return map[role] || 'bg-gray-100 text-gray-800';
            },

            // ===== API =====
            async fetchUsers() {
                this.loading = true;
                this.error = '';
                try {
                    const params = new URLSearchParams();
                    if (this.search) params.append('search', this.search);
                    if (this.roleFilter) params.append('role', this.roleFilter);
                    const page = this.pagination?.current_page || 1;
                    params.append('page', page);

                    const res = await fetch(`${window.AppUsers.urlIndex}?${params}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.AppUsers.csrfToken }
                    });
                    if (!res.ok) throw new Error('Ошибка загрузки пользователей.');
                    const data = await res.json();
                    this.users = data.data;
                    this.pagination = {
                        current_page: data.current_page, last_page: data.last_page,
                        from: data.from, to: data.to, total: data.total,
                        prev_page_url: data.prev_page_url, next_page_url: data.next_page_url,
                    };
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },

            goToPage(page) {
                if (page < 1 || page > (this.pagination?.last_page || 1)) return;
                this.pagination.current_page = page;
                this.fetchUsers();
            },

            get paginationLinks() {
                if (!this.pagination) return [];
                const current = this.pagination.current_page, last = this.pagination.last_page;
                const links = [], delta = 2;
                for (let i = 1; i <= last; i++) {
                    if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
                        links.push(i);
                    } else if (links[links.length - 1] !== '...') {
                        links.push('...');
                    }
                }
                return links;
            },

            // ===== Модальное окно: Создание =====
            openCreateModal() {
                this.editingUser = null;
                this.userForm = { name: '', email: '', role: 'user', password: '' };
                this.userFormErrors = {};
                this.showUserModal = true;
            },

            openEditModal(user) {
                this.editingUser = user;
                this.userForm = { name: user.name, email: user.email, role: user.role, password: '' };
                this.userFormErrors = {};
                this.showUserModal = true;
            },

            closeUserModal() {
                this.showUserModal = false;
                this.editingUser = null;
                this.userFormErrors = {};
                this.userFormSubmitting = false;
            },

            validateUserForm() {
                this.userFormErrors = {};
                if (!this.userForm.name || this.userForm.name.trim().length < 2) {
                    this.userFormErrors.name = 'Имя должно содержать минимум 2 символа.';
                }
                if (!this.userForm.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.userForm.email)) {
                    this.userFormErrors.email = 'Введите корректный email.';
                }
                if (!this.editingUser && (!this.userForm.password || this.userForm.password.length < 8)) {
                    this.userFormErrors.password = 'Пароль должен содержать минимум 8 символов.';
                }
                if (this.editingUser && this.userForm.password && this.userForm.password.length < 8) {
                    this.userFormErrors.password = 'Пароль должен содержать минимум 8 символов.';
                }
                return Object.keys(this.userFormErrors).length === 0;
            },

            async submitUserForm() {
                if (!this.validateUserForm()) return;
                this.userFormSubmitting = true;

                const isEdit = !!this.editingUser;
                const url = isEdit ? window.AppUsers.urlUpdate.replace('__ID__', this.editingUser.id) : window.AppUsers.urlStore;
                const method = isEdit ? 'PUT' : 'POST';

                const body = { name: this.userForm.name, email: this.userForm.email, role: this.userForm.role };
                if (this.userForm.password) body.password = this.userForm.password;

                try {
                    const res = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.AppUsers.csrfToken },
                        body: JSON.stringify(body),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        if (data.errors) {
                            const serverErrors = {};
                            for (const [key, msgs] of Object.entries(data.errors)) {
                                serverErrors[key] = Array.isArray(msgs) ? msgs[0] : msgs;
                            }
                            this.userFormErrors = serverErrors;
                            return;
                        }
                        throw new Error(data.message || 'Ошибка сохранения.');
                    }
                    this.closeUserModal();
                    this.flashMessage = data.message;
                    await this.fetchUsers();
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.userFormSubmitting = false;
                }
            },

            // ===== Модальное окно: Удаление =====
            confirmDelete(user) {
                this.deletingUser = user;
                this.showDeleteModal = true;
            },
            closeDeleteModal() {
                this.showDeleteModal = false;
                this.deletingUser = null;
                this.deleteSubmitting = false;
            },
            async deleteUser() {
                if (!this.deletingUser) return;
                this.deleteSubmitting = true;
                try {
                    const res = await fetch(window.AppUsers.urlDestroy.replace('__ID__', this.deletingUser.id), {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.AppUsers.csrfToken },
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Ошибка удаления.');
                    this.closeDeleteModal();
                    this.flashMessage = data.message;
                    await this.fetchUsers();
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.deleteSubmitting = false;
                }
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                return d.toLocaleDateString('ru-RU', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            },
        };
    }
</script>
@endverbatim
@endpush
