<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    //
    protected $fillable = [
        'grade',
        // 'level_id',
    ];
    // relations
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
