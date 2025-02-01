<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    //
    // uuid
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'role'
    ];

    // relations
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
