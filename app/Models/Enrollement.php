<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollement extends Model
{
    //
    // uuid
    use HasUuids;
    use SoftDeletes;

    // relations
    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
}
