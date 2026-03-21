<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function competence()
    {
        return $this->belongsTo(Competence::class);
    }
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }
}
