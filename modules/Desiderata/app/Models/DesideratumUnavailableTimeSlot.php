<?php

declare(strict_types=1);

namespace Modules\Desiderata\Models;

use App\Enums\WeekdayEnum;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesideratumUnavailableTimeSlot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'desideratum_id',
        'day',
        'time_slot_id',
    ];

    protected $casts = [
        'day' => WeekdayEnum::class,
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

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
