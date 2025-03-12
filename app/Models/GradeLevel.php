<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    //
    protected $table = 'grade_level';
    // relations
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
