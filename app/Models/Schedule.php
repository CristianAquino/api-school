<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['start_time', 'end_time'];

    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    // relations
    public function courses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps()->withPivot('id', 'day');
    }
}
