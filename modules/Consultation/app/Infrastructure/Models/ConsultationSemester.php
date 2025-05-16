<?php

declare(strict_types=1);

namespace Modules\Consultation\Infrastructure\Models;

use App\Enums\WeekdayEnum;
use App\Enums\WeekTypeEnum;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'scientific_worker_id',
        'semester_id',
        'day',
        'week_type',
        'start_time',
        'end_time',
        'location',
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
