<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    public function courses()
    {
        return $this->hasMany(Course::class)->orderBy('order');
    }
}
