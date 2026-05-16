# Портал чемпионатного движения «Профессионалы»

Веб-платформа для управления компетенциями, учебными модулями, приёмом и проверкой конкурсных заданий, а также документацией в рамках всероссийского чемпионатного движения «Профессионалы». Система объединяет участников, экспертов и администраторов в едином рабочем пространстве.

---

## Содержание

- [Назначение и решаемые задачи](#назначение-и-решаемые-задачи)
- [Ключевые возможности](#ключевые-возможности)
- [Поддерживаемые окружения](#поддерживаемые-окружения)
- [Установка и настройка](#установка-и-настройка)
- [Примеры использования](#примеры-использования)
- [Архитектура и технологический стек](#архитектура-и-технологический-стек)
- [Структура проекта](#структура-проекта)
- [Лицензия](#лицензия)

---

## Назначение и решаемые задачи

Платформа создана для цифровизации процесса подготовки и проведения чемпионатов профессионального мастерства. Она решает следующие задачи:

- **Централизованный каталог компетенций** — организаторы и эксперты могут создавать, редактировать и публиковать компетенции с привязанными учебными модулями.
- **Приём и проверка конкурсных заданий** — участники загружают свои работы (видео, архивы с проектами, документацию), а эксперты проверяют их, выставляют оценки и дают обратную связь.
- **Документационное обеспечение** — хранение и распространение положений, регламентов, расписаний, банков заданий в форматах PDF, DOCX, XLSX с возможностью просмотра прямо в браузере.
- **Управление пользователями** — гибкая система ролей, блокировка нарушителей, возможность администратора работать от имени любого пользователя (имперсонация).
- **Отслеживание прогресса** — учёт прохождения уроков и тестов в рамках каждого модуля.

### Роли пользователей

| Роль | Описание | Ключевые права |
|------|----------|----------------|
| **user** | Участник чемпионата | Отправка заданий, просмотр компетенций, модулей и документации |
| **expert** | Эксперт-проверяющий | Проверка и оценка заданий, управление компетенциями, модулями и документами |
| **admin** | Администратор системы | Все права эксперта + управление пользователями, имперсонация |

---

## Ключевые возможности

### Каталог компетенций

- Древовидная структура: **Компетенция → Модуль → Урок → Квиз (тест)**
- Каждая компетенция содержит описание, изображение и список модулей
- Модули упорядочены и содержат учебный контент (`content`) с полноценной вёрсткой
- Уроки имеют порядковый номер, контент и опциональный тест для проверки знаний
- Квизы поддерживают настройку максимального числа попыток (`max_attempts`) и проходного балла (`passing_score`)
- Публичная лендинг-страница с 6 последними компетенциями и быстрыми ссылками на документацию
- Кэширование главной страницы на 1 час для снижения нагрузки

### Система заданий (Submissions)

- Загрузка файлов до **200 МБ** (форматы: `zip`, `mp4`, `mov`, `avi`)
- Хранение в [`storage/app/public/submissions/`](storage/app/public/)
- Возможность прикрепить текстовый комментарий к работе
- Статусы заданий: `pending` (ожидает проверки), `approved` (принято), `revision` (требует доработки)
- Эксперт может выставить **оценку** (`grade`) с точностью до двух знаков после запятой (0–999.99)
- Скачивание файлов через контроллер (без зависимости от `symlink`)
- Фильтрация заданий по статусу в панели эксперта
- Постраничная навигация (20 элементов на странице)

### Документация

- Три тематических раздела: **Банк заданий**, **Положения**, **Расписания**
- Поддерживаемые форматы: `PDF`, `DOCX`, `XLSX` (до 50 МБ каждый)
- Просмотр PDF прямо в браузере (inline)
- Предпросмотр офисных документов через **Microsoft Office Web Viewer** с использованием временных подписанных ссылок (5 минут)
- Скачивание файлов с сохранением оригинального имени
- Автоматическое определение MIME-типа и размера
- Человекочитаемое отображение размера файла (Б/КБ/МБ)
- Иконки типов файлов на лендинге и в разделе документации

### Администрирование

- **Панель управления пользователями** с AJAX-интерфейсом:
  - Поиск по имени и email
  - Фильтрация по роли
  - Сортировка по любому полю (имя, email, роль, дата создания)
  - Создание, редактирование и удаление пользователей
  - Быстрая смена роли (inline)
  - Блокировка/разблокировка пользователей
- **Имперсонация** — администратор может войти в систему от имени любого пользователя и вернуться обратно
- Защита от самоблокировки и самоудаления

### Безопасность

- Авторизация через **Gates** ([`AppServiceProvider`](app/Providers/AppServiceProvider.php)):
  - `submission-manage` — доступ к отправке заданий (`user`, `admin`)
  - `document-manage` — загрузка и удаление документов (`admin`, `expert`)
  - `competence-manage` — CRUD компетенций (`admin`, `expert`)
  - `module-manage` — CRUD модулей (`admin`, `expert`)
- Middleware-проверки: [`AdminMiddleware`](app/Http/Middleware/AdminMiddleware.php), [`ExpertMiddleware`](app/Http/Middleware/ExpertMiddleware.php)
- Валидация всех входящих данных через Form Requests и inline-валидацию
- Защита от массового присвоения (`$fillable`)
- Временные подписанные URL для доступа к файлам из Office Web Viewer
- CSRF-защита всех форм и AJAX-запросов

### Аутентификация (Laravel Breeze)

- Регистрация с выбором роли (по умолчанию — `user`)
- Вход по email и паролю
- Подтверждение email (опционально)
- Восстановление пароля
- Подтверждение пароля перед критическими действиями
- Профиль пользователя с возможностью редактирования

### Отслеживание прогресса

- Модель [`UserProgress`](app/Models/UserProgress.php) фиксирует:
  - Факт завершения урока (`completed`)
  - Набранный балл (`score`)
  - Дату и время завершения (`completed_at`)
- Привязка к конкретному пользователю и уроку

---

## Поддерживаемые окружения

| Требование | Версия |
|------------|--------|
| **PHP** | 8.3+ |
| **Веб-сервер** | Nginx, Apache (с `mod_rewrite`), или встроенный сервер Laravel (`php artisan serve`) |
| **База данных** | SQLite (разработка), MySQL 8+ / MariaDB 10.3+ (продакшен) |
| **Node.js** | 18+ (для сборки фронтенда) |
| **ОС** | Linux, macOS, Windows 10/11 (с WSL2 или Laragon/XAMPP) |

---

## Установка и настройка

### 1. Клонирование репозитория

```bash
git clone <repo-url> professionals-site
cd professionals-site
```

### 2. Установка зависимостей

```bash
# PHP-зависимости
composer install

# Node.js-зависимости и сборка фронтенда
npm install
npm run build
```

### 3. Настройка окружения

```bash
# Копирование файла окружения
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate
```

Настройте подключение к базе данных в [`.env`](.env.example):

```env
# Для SQLite (разработка):
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Для MySQL (продакшен):
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=professionals
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Миграция и наполнение базы

```bash
# Запуск миграций
php artisan migrate

# (Опционально) Наполнение тестовыми данными
php artisan db:seed
```

Сидер [`CompetenceSeeder`](database/seeders/CompetenceSeeder.php) создаёт тестовые компетенции и модули.

### 5. Символическая ссылка для хранилища

```bash
php artisan storage:link
```

Это создаст симлинк `public/storage` → `storage/app/public/`, необходимый для доступа к загруженным файлам.

### 6. Запуск сервера разработки

```bash
composer run dev
```

Эта команда одновременно запускает:
- **PHP-сервер** (`php artisan serve`) — http://localhost:8000
- **Vite** (`npm run dev`) — горячая перезагрузка фронтенда
- **Очереди** (`php artisan queue:listen`) — обработка фоновых задач
- **Логирование** (`php artisan pail`) — потоковый просмотр логов

### Быстрая установка одной командой

```bash
composer run setup
```

Выполняет: `composer install`, копирование `.env`, генерацию ключа, миграции, `npm install` и сборку фронтенда.

---

## Примеры использования

### Отправка задания участником

```php
// app/Http/Controllers/SubmissionController.php
public function store(Request $request, Module $module)
{
    $request->validate([
        'file'    => 'required|file|mimes:zip,mp4,mov,avi|max:204800',
        'comment' => 'nullable|string',
    ]);

    $path = $request->file('file')->store('submissions', 'public');
    $fileUrl = Storage::url($path);

    Submission::create([
        'user_id'   => Auth::id(),
        'module_id'  => $module->id,
        'file_url'  => $fileUrl,
        'comment'   => $request->comment,
        'status'    => 'pending',
    ]);

    return redirect()->back()->with('success', 'Задание отправлено на проверку.');
}
```

### Проверка задания экспертом с оценкой

```php
// app/Http/Controllers/ExpertSubmissionController.php
public function update(Request $request, Submission $submission)
{
    $validated = $request->validate([
        'status'   => 'required|in:pending,approved,revision',
        'feedback' => 'nullable|string|max:2000',
        'grade'    => 'nullable|numeric|min:0|max:999.99',
    ]);

    $submission->update([
        'status'   => $validated['status'],
        'feedback' => $validated['feedback'] ?? null,
        'grade'    => $validated['grade'] ?? null,
    ]);

    return redirect()->route('expert.submissions.index')
        ->with('success', 'Статус задания обновлён.');
}
```

### Имперсонация (вход под пользователем)

```php
// Администратор входит от имени участника
POST /impersonate/{user}

// Возврат в режим администратора
POST /stop-impersonate
```

### Работа с документами

```php
// Загрузка документа (admin/expert)
POST /documentation
// Поля: title, section (bank|regulations|schedules), file (pdf|docx|xlsx, ≤50MB)

// Предпросмотр DOCX/XLSX через Office Web Viewer
GET /documentation/{document}/preview

// Скачивание
GET /documentation/{document}/download

// Inline-просмотр PDF
GET /documentation/{document}/view
```

### Управление пользователями через API

Все эндпоинты администратора возвращают JSON и работают через AJAX:

```javascript
// Пример: быстрая смена роли пользователя
fetch(`/admin/users/${userId}/role`, {
    method: 'PATCH',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ role: 'expert' }),
});
```

---

## Архитектура и технологический стек

### Бэкенд

| Технология | Назначение |
|------------|------------|
| **Laravel 13** | PHP-фреймворк — маршрутизация, контроллеры, ORM, валидация, очереди, кэш |
| **PHP 8.3** | Язык программирования |
| **Eloquent ORM** | Работа с базой данных через модели |
| **SQLite / MySQL** | Хранение данных |
| **Laravel Breeze** | Аутентификация и шаблоны профиля |
| **Gates** | Авторизация на основе ролей |
| **Laravel Storage** | Файловое хранилище (`storage/app/public/`) |

### Фронтенд

| Технология | Назначение |
|------------|------------|
| **Blade** | Шаблонизатор Laravel |
| **Tailwind CSS 4** | Utility-first CSS-фреймворк |
| **Vite** | Сборка фронтенд-ассетов |
| **Alpine.js** (в составе Breeze) | Реактивность на клиенте |
| **Vanilla JS (Fetch API)** | AJAX-запросы в админ-панели |

### Модель данных (ER-диаграмма)

```
User (id, name, email, password, role, is_blocked)
 ├── Submission (id, user_id, module_id, file_url, comment, status, feedback, grade)
 │    └── Module (id, competence_id, title, slug, description, order, content)
 │         ├── Competence (id, title, slug, description, image)
 │         └── Lesson (id, module_id, title, content, order)
 │              └── Quiz (id, lesson_id, title, description, max_attempts, passing_score)
 ├── UserProgress (id, user_id, lesson_id, completed, score, completed_at)
 │
 └── Document (id, title, section, filename, file_path, mime_type, file_size)
```

### Система авторизации

```
                   ┌─────────────┐
                   │   Request   │
                   └──────┬──────┘
                          │
                   ┌──────▼──────┐
                   │  auth       │  ← Breeze middleware
                   └──────┬──────┘
                          │
          ┌───────────────┼───────────────┐
          │               │               │
   ┌──────▼──────┐ ┌──────▼──────┐ ┌──────▼──────┐
   │   admin      │ │   expert    │ │    Gate     │
   │ middleware   │ │ middleware  │ │   checks    │
   └──────┬──────┘ └──────┬──────┘ └──────┬──────┘
          │               │               │
   ┌──────▼───────────────▼───────────────▼──────┐
   │           Controller / Action               │
   └─────────────────────────────────────────────┘
```

### Маршрутизация

| Группа | Middleware | Описание |
|--------|------------|----------|
| `/` | нет | Лендинг (гостевой доступ) |
| `/competences/*`, `/modules/*`, `/submissions/*` | `auth` | Основной функционал |
| `/expert/submissions/*` | `auth` + `expert` | Проверка заданий |
| `/admin/users/*` | `auth` + `admin` | Управление пользователями |
| `/impersonate/*` | `auth` + `admin` | Имперсонация |
| `/register`, `/login`, `/forgot-password` | `guest` | Аутентификация |
| `/documentation/serve/*` | нет (signed URL) | Отдача файлов для OWV |

---

## Структура проекта

```
professionals-site/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/AdminUserController.php    # CRUD пользователей (AJAX)
│   │   │   ├── Auth/                            # Аутентификация Breeze
│   │   │   ├── CompetenceController.php         # CRUD компетенций
│   │   │   ├── CourseController.php             # (deprecated) Редиректы
│   │   │   ├── DocumentationController.php      # Управление документами
│   │   │   ├── ExpertSubmissionController.php   # Проверка заданий
│   │   │   ├── ModuleController.php             # CRUD модулей
│   │   │   ├── ProfileController.php            # Профиль пользователя
│   │   │   ├── SubmissionController.php         # Отправка заданий
│   │   │   └── UserController.php               # Имперсонация
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php              # Проверка роли admin
│   │   │   ├── ExpertMiddleware.php             # Проверка роли expert/admin
│   │   │   └── ImpersonateMiddleware.php        # Поддержка имперсонации
│   │   └── Requests/                            # Form Requests
│   ├── Models/
│   │   ├── Competence.php                       # Компетенция
│   │   ├── Course.php                           # (deprecated)
│   │   ├── Document.php                         # Документ
│   │   ├── Lesson.php                           # Урок
│   │   ├── Module.php                           # Модуль
│   │   ├── Quiz.php                             # Квиз
│   │   ├── Submission.php                       # Задание
│   │   ├── User.php                             # Пользователь
│   │   └── UserProgress.php                     # Прогресс
│   └── Providers/
│       └── AppServiceProvider.php               # Gate-определения
├── database/
│   ├── migrations/                              # Миграции
│   └── seeders/
│       ├── CompetenceSeeder.php                 # Тестовые компетенции
│       └── DatabaseSeeder.php                   # Главный сидер
├── resources/
│   └── views/
│       ├── auth/                                # Страницы аутентификации
│       ├── competences/                         # Каталог компетенций
│       ├── components/                          # Blade-компоненты
│       ├── courses/                             # (deprecated)
│       ├── documentation/                       # Раздел документации
│       ├── expert/submissions/                  # Панель эксперта
│       ├── layouts/                             # Макеты (app, guest, nav)
│       ├── modules/                             # Страница модуля
│       ├── profile/admin/                       # Админ-панель пользователей
│       ├── submissions/                         # История заданий
│       └── welcome.blade.php                    # Лендинг
├── routes/
│   ├── auth.php                                 # Маршруты Breeze
│   └── web.php                                  # Основные маршруты
└── tests/
    ├── Feature/
    │   ├── AdminUserTest.php                    # Тесты админ-панели
    │   ├── ProfileTest.php                      # Тесты профиля
    │   └── Auth/                                # Тесты аутентификации
    └── Unit/
        └── ExampleTest.php
```

---

## Команды

| Команда | Описание |
|---------|----------|
| `composer run setup` | Полная установка проекта с нуля |
| `composer run dev` | Запуск сервера разработки (PHP + Vite + Queue + Pail) |
| `composer run test` | Запуск тестов PHPUnit |
| `php artisan migrate` | Применение миграций |
| `php artisan migrate:fresh --seed` | Полный сброс БД с наполнением |
| `php artisan db:seed` | Наполнение БД тестовыми данными |
| `php artisan storage:link` | Создание симлинка для публичного хранилища |
| `php artisan optimize` | Кэширование конфигурации и маршрутов (продакшен) |
| `npm run dev` | Запуск Vite в режиме разработки |
| `npm run build` | Сборка фронтенда для продакшена |

---

## Лицензия

Проект распространяется под лицензией [MIT](https://opensource.org/licenses/MIT). Подробности см. в файле [LICENSE](LICENSE) (при наличии).

Фреймворк Laravel, используемый в проекте, также лицензирован под MIT.

---

## Дополнительная документация

- [Официальная документация Laravel 13](https://laravel.com/docs)
- [Документация Tailwind CSS](https://tailwindcss.com/docs)
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze)
- [Движение «Профессионалы»](https://esim.firpo.ru/)
