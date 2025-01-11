<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    //
    protected $table = 'course_schedule';
    protected $fillable = [
        'day',
    ];
    // relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
