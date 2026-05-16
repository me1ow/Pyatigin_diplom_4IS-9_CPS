<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
    ];

    /**
     * Маршрутизация по slug вместо id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Модули, напрямую привязанные к компетенции.
     */
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Курсы (сохранено для обратной совместимости, если есть).
     */
    public function courses()
    {
        return $this->hasMany(Course::class)->orderBy('order');
    }
}
