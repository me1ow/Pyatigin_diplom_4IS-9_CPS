<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Миграция перестраивает таблицу modules:
     * - Добавляет прямую связь с competences (competence_id)
     * - Добавляет slug для маршрутизации по имени
     * - Добавляет description для карточек модулей
     * - Переносит существующие данные из связи course → competence
     * - Удаляет столбец course_id
     */
    public function up(): void
    {
        // Шаг 1: Добавляем новые столбцы (nullable на время переноса данных)
        Schema::table('modules', function (Blueprint $table) {
            $table->foreignId('competence_id')->nullable()->after('course_id');
            $table->string('slug')->nullable()->after('title');
            $table->text('description')->nullable()->after('slug');
        });

        // Шаг 2: Переносим competence_id из таблицы courses
        DB::statement('
            UPDATE modules
            SET competence_id = (
                SELECT competence_id FROM courses WHERE courses.id = modules.course_id
            )
        ');

        // Шаг 3: Генерируем slug для существующих модулей (на основе title)
        $modules = DB::table('modules')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($modules as $module) {
            $baseSlug = Str::slug($module->title);
            $slug = $baseSlug;
            $counter = 1;

            // Гарантируем уникальность slug
            while (DB::table('modules')->where('slug', $slug)->where('id', '!=', $module->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('modules')->where('id', $module->id)->update(['slug' => $slug]);
        }

        // Шаг 4: Делаем столбцы not nullable и добавляем уникальный индекс на slug
        Schema::table('modules', function (Blueprint $table) {
            // Делаем competence_id not nullable
            $table->foreignId('competence_id')->nullable(false)->change();
            // Добавляем внешний ключ
            $table->foreign('competence_id')->references('id')->on('competences')->onDelete('cascade');

            // Делаем slug not nullable и уникальным
            $table->string('slug')->nullable(false)->unique()->change();
        });

        // Шаг 5: Удаляем course_id
        if (Schema::hasColumn('modules', 'course_id')) {
            // Сначала удаляем внешний ключ, если он существует
            // Имя внешнего ключа Laravel генерирует как {table}_{column}_foreign
            Schema::table('modules', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }
    }

    /**
     * Откат: возвращаем course_id и удаляем добавленные столбцы.
     */
    public function down(): void
    {
        // Восстанавливаем course_id
        Schema::table('modules', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('id');
        });

        // Пытаемся восстановить данные из competence_id
        DB::statement('
            UPDATE modules
            SET course_id = (
                SELECT id FROM courses WHERE courses.competence_id = modules.competence_id LIMIT 1
            )
        ');

        // Удаляем добавленные столбцы и внешние ключи
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['competence_id']);
            $table->dropColumn(['competence_id', 'slug', 'description']);

            // Делаем course_id not nullable и добавляем внешний ключ
            $table->foreignId('course_id')->nullable(false)->change();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
};
