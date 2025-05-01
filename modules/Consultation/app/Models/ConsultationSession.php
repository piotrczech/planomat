<?php

declare(strict_types=1);

namespace Modules\Consultation\Models;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'scientific_worker_id',
        'semester_id',
        'consultation_date',
        'start_time',
        'end_time',
        'location',
    ];

    protected $casts = [
        'consultation_date' => 'date',
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
