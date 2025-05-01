<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Desiderata\Models\DesideratumUnavailableTimeSlot;

final class TimeSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function unavailableDesideratumTimeSlots(): HasMany
    {
        return $this->hasMany(DesideratumUnavailableTimeSlot::class);
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . '-' . $this->end_time->format('H:i');
    }
}
