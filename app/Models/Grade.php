<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    //
    protected $fillable = [
        'grade',
    ];
    // relations
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }
    public function levels()
    {
        return $this->belongsToMany(Level::class)->withTimestamps()->withPivot('id');
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
