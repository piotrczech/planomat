<?php

declare(strict_types=1);

namespace Modules\Desiderata\Models;

use App\Models\Course;
use App\Models\Semester;
use App\Models\User;
use App\Enums\CoursePreferenceTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * Pobiera wszystkie preferencje kursów
     */
    public function coursePreferences(): HasMany
    {
        return $this->hasMany(DesideratumCoursePreference::class);
    }

    /**
     * Relacja do wszystkich kursów
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'desideratum_course_preferences',
            'desideratum_id',
            'course_id',
        )->withPivot('type');
    }

    /**
     * Pobiera kursy, które pracownik chce prowadzić
     */
    public function wantedCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'desideratum_course_preferences',
            'desideratum_id',
            'course_id',
        )->wherePivot('type', CoursePreferenceTypeEnum::WANTED->value);
    }

    /**
     * Pobiera kursy, które pracownik mógłby prowadzić
     */
    public function couldCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'desideratum_course_preferences',
            'desideratum_id',
            'course_id',
        )->wherePivot('type', CoursePreferenceTypeEnum::COULD->value);
    }

    /**
     * Pobiera kursy, których pracownik nie chce prowadzić
     */
    public function notWantedCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'desideratum_course_preferences',
            'desideratum_id',
            'course_id',
        )->wherePivot('type', CoursePreferenceTypeEnum::UNWANTED->value);
    }

    /**
     * Pobiera niedostępne sloty czasowe
     */
    public function unavailableTimeSlots(): HasMany
    {
        return $this->hasMany(DesideratumUnavailableTimeSlot::class);
    }
}
