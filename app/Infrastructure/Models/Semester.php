<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Enums\SemesterSeasonEnum;
use Modules\Consultation\Infrastructure\Models\SemesterConsultation;
use Modules\Consultation\Infrastructure\Models\SessionConsultation;
use Modules\Consultation\Infrastructure\Models\PartTimeConsultation;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'season',
        'semester_start_date',
        'session_start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'semester_start_date' => 'date',
        'session_start_date' => 'date',
        'end_date' => 'date',
        'season' => SemesterSeasonEnum::class,
        'is_active' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return $this->season->label();
    }

    public function getAcademicYearAttribute(): string
    {
        return $this->start_year . '/' . ($this->start_year + 1);
    }

    public static function getCurrentSemester()
    {
        $activeSemester = self::where('is_active', true)->first();

        if ($activeSemester) {
            return $activeSemester;
        }

        return null;
    }

    public function semesterConsultations(): HasMany
    {
        return $this->hasMany(SemesterConsultation::class, 'semester_id');
    }

    public function sessionConsultations(): HasMany
    {
        return $this->hasMany(SessionConsultation::class, 'semester_id');
    }

    public function partTimeConsultations(): HasMany
    {
        return $this->hasMany(PartTimeConsultation::class, 'semester_id');
    }

    public function desiderata(): HasMany
    {
        return $this->hasMany(Desideratum::class, 'semester_id');
    }
}
