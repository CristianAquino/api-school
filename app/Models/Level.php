<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'level',
    ];
    // relations
    public function grades()
    {
        return $this->belongsToMany(Grade::class)->withPivot('id')->withTimestamps();
    }
}