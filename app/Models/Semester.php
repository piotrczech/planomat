<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'season',
        'semester_start_date',
        'session_start_date',
        'end_date',
    ];

    protected $casts = [
        'semester_start_date' => 'date',
        'session_start_date' => 'date',
        'end_date' => 'date',
    ];

    public static function getCurrentSemester()
    {
        $now = Carbon::now();

        return self::where('semester_start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();
    }
}
