<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = ['start_time', 'end_time'];

    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    // relations
    public function courses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps()->withPivot('id', 'day');
    }
}
