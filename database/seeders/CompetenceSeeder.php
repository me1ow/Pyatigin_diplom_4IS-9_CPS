<?php

namespace Database\Seeders;

use App\Models\Competence;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class CompetenceSeeder extends Seeder
{
    public function run(): void
    {
        // Создаём компетенцию "Моушн-дизайн"
        $competence = Competence::create([
            'title' => 'Моушн-дизайн',
            'slug' => 'motion-design',
            'description' => 'Создание анимированной графики, видеороликов, визуальных эффектов.',
            'image' => null,
        ]);

        // Курс
        $course = Course::create([
            'competence_id' => $competence->id,
            'title' => 'Основы моушн-дизайна',
            'order' => 1,
        ]);

        // 5 модулей
        $modules = [
            ['title' => 'Введение в моушн-дизайн', 'order' => 1, 'content' => '<h2>Что такое моушн-дизайн?</h2><p>Моушн-дизайн — это искусство создания анимированной графики. В этом модуле вы узнаете основные принципы и инструменты.</p>'],
            ['title' => 'Adobe After Effects: интерфейс и основы', 'order' => 2, 'content' => '<h2>Знакомство с After Effects</h2><p>Разберём интерфейс, создание композиций, работу со слоями и ключевыми кадрами.</p>'],
            ['title' => 'Анимация форм и текста', 'order' => 3, 'content' => '<h2>Анимация форм и текста</h2><p>Изучим анимацию векторных фигур, работу с текстом и пресетами.</p>'],
            ['title' => 'Работа с масками и эффектами', 'order' => 4, 'content' => '<h2>Маски и эффекты</h2><p>Научимся использовать маски для создания сложных переходов и применять эффекты.</p>'],
            ['title' => 'Итоговый проект', 'order' => 5, 'content' => '<h2>Создание заставки для канала</h2><p>Примените все полученные знания для создания 10-секундной заставки.</p>'],
        ];

        foreach ($modules as $mod) {
            Module::create(array_merge($mod, ['course_id' => $course->id]));
        }
    }
}