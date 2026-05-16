<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'competence_id',
        'title',
        'slug',
        'description',
        'order',
        'content',
    ];

    /**
     * Маршрутизация по slug вместо id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Связи
    public function competence(): BelongsTo
    {
        return $this->belongsTo(Competence::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
}