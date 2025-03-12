<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'year',
        'start_date',
        'end_date',
    ];

    // relations
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }
}
