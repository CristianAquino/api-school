<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Enrollement extends Model
{
    //
    // uuid
    use HasUuids;

    // relations
    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
