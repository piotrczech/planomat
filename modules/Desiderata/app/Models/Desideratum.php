<?php

declare(strict_types=1);

namespace Modules\Desiderata\Models;

use App\Models\Course;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desideratum extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'scientific_worker_id',
        'want_stationary',
        'want_non_stationary',
        'agree_to_overtime',
        'master_theses_count',
        'bachelor_theses_count',
        'max_hours_per_day',
        'max_consecutive_hours',
        'additional_notes',
    ];

    protected $casts = [
        'want_stationary' => 'boolean',
        'want_non_stationary' => 'boolean',
        'agree_to_overtime' => 'boolean',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function scientificWorker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scientific_worker_id');
    }

    public function wantedCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'id', 'course_id')
            ->where('status', 'wanted');
    }

    public function couldCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'id', 'course_id')
            ->where('status', 'could');
    }

    public function notWantedCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'id', 'course_id')
            ->where('status', 'not_want');
    }

    public function unavailableTimeSlots(): HasMany
    {
        return $this->hasMany(DesideratumUnavailableTimeSlot::class);
    }
}
