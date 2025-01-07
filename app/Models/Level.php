<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    //
    protected $fillable = [
        'level',
    ];
    // relations
    public function grades()
    {
        return $this->belongsToMany(Grade::class)->withPivot('id')->withTimestamps();
    }
}
