<?php

declare(strict_types=1);

namespace Modules\Desiderata\Models;

use App\Enums\CoursePreferenceTypeEnum;
use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesideratumCoursePreference extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'desideratum_id',
        'course_id',
        'type',
    ];

    protected $casts = [
        'type' => CoursePreferenceTypeEnum::class,
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            $model->updated_at = $model->freshTimestamp();
        });
    }

    public function desideratum(): BelongsTo
    {
        return $this->belongsTo(Desideratum::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
