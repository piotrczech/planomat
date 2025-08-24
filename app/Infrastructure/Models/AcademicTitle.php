<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AcademicTitle extends Model
{
    public $timestamps = false;

    protected $table = 'academic_titles';

    protected $primaryKey = 'title';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'academic_title', 'title');
    }
}
