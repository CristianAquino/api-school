<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = [
        'course',
        'description',
    ];
    // relations
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
