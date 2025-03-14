<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Student extends Model
{
    //
    // uuid
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'role'
    ];

    // relations
    public function enrollements()
    {
        return $this->hasMany(Enrollement::class);
    }
    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
