<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    //

    protected $fillable = [
        'number_note',
        'letter_note',
        'avg',
    ];

    public const LETTER_NOTES = ['AD', 'A', 'B', 'C',];

    // relations
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // redondeo de avg a dos decimales
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->avg = round($model->avg, 2);
        });
    }
}
