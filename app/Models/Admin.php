<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Admin extends Model
{
    //
    // uuid
    use HasUuids;

    protected $fillable = [
        'code_admin',
        'role'
    ];

    // relations
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
