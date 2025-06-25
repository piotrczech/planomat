<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Models;

use App\Domain\Enums\WeekdayEnum;
use App\Domain\Enums\WeekTypeEnum;
use App\Infrastructure\Models\Semester;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterConsultation extends Model
{
    protected $fillable = [
        'scientific_worker_id',
        'semester_id',
        'day',
        'week_type',
        'start_time',
        'end_time',
        'location_building',
        'location_room',
    ];

    protected $casts = [
        'day' => WeekdayEnum::class,
        'week_type' => WeekTypeEnum::class,
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function scientificWorker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scientific_worker_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
