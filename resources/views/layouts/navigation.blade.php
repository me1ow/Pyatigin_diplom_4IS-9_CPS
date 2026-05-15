<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    {{-- Баннер имперсонации (только для авторизованных) --}}
    @auth
        @if(session()->has('impersonator_id'))
            <div class="bg-amber-500 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-sm font-medium">
                            {{ __('Режим имперсонации') }} — {{ __('Вы просматриваете сайт как') }}
                            <strong>{{ Auth::user()->name }}</strong>
                            ({{ Auth::user()->role }})
                        </span>
                    </div>
                    <form method="POST" action="{{ route('stop.impersonate') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1 bg-white text-amber-700 rounded-md text-sm font-semibold hover:bg-amber-50 transition">
                            {{ __('Вернуться к администрированию') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endauth

    <!-- Основное содержимое навигации -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Логотип -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('competences.index') }}" class="flex items-center">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        <span class="ml-2 text-lg font-semibold text-gray-800 hidden sm:block">{{ __('Профессионалы') }}</span>
                    </a>
                </div>

                <!-- Основные ссылки (только для авторизованных) -->
                @auth
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('competences.index')" :active="request()->routeIs('competences.*')">
                            {{ __('Компетенции') }}
                        </x-nav-link>
                        <x-nav-link :href="route('submissions.index')" :active="request()->routeIs('submissions.*')">
                            {{ __('Мои задания') }}
                        </x-nav-link>
                    </div>
                @endauth

                <!-- Ссылки для гостей -->
                @guest
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 transition">
                            {{ __('Войти') }}
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-1 border border-indigo-600 text-sm font-medium rounded-md text-indigo-600 hover:bg-indigo-50 transition">
                            {{ __('Регистрация') }}
                        </a>
                    </div>
                @endguest
            </div>

            <!-- Выпадающее меню пользователя (только для авторизованных) -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                {{-- Индикатор роли --}}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if(Auth::user()->isAdmin()) bg-red-100 text-red-800
                                    @elseif(Auth::user()->isExpert()) bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ Auth::user()->role }}
                                </span>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Профиль') }}
                            </x-dropdown-link>

                            {{-- Управление пользователями (только для админа) --}}
                            @if(Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('profile.edit') . '#users'">
                                    {{ __('Управление пользователями') }}
                                </x-dropdown-link>
                            @endif

                            {{-- Разделитель --}}
                            <div class="border-t border-gray-200"></div>

                            <!-- Выход -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Выйти') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth

            <!-- Кнопка бургер-меню (мобильная версия) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @auth
            <!-- Основные ссылки -->
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('competences.index')" :active="request()->routeIs('competences.*')">
                    {{ __('Компетенции') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('submissions.index')" :active="request()->routeIs('submissions.*')">
                    {{ __('Мои задания') }}
                </x-responsive-nav-link>
            </div>

            <!-- Информация о пользователе -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        @if(Auth::user()->isAdmin()) bg-red-100 text-red-800
                        @elseif(Auth::user()->isExpert()) bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ Auth::user()->role }}
                    </span>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Профиль') }}
                    </x-responsive-nav-link>

                    <!-- Выход -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Выйти') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endguest

        @guest
            <!-- Ссылки для гостей -->
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Войти') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Регистрация') }}
                </x-responsive-nav-link>
            </div>
        @endguest
    </div>
</nav>
