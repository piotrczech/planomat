<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            $model->created_at = now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
