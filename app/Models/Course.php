<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = [
        'course',
        'description',
        'grade_level_id',
        'day'
    ];
    // relations
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class)->withTimestamps()->withPivot('id', 'day');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
