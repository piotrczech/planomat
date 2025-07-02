<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use App\Domain\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Lab404\Impersonate\Models\Impersonate;
use Modules\Consultation\Infrastructure\Models\SemesterConsultation;
use Modules\Consultation\Infrastructure\Models\SessionConsultation;
use Modules\Consultation\Infrastructure\Models\PartTimeConsultation;
use Modules\Desiderata\Infrastructure\Models\Desideratum;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Impersonate, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'usos_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class,
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function hasRole(RoleEnum $role): bool
    {
        return $this->role === $role;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole(RoleEnum::ADMINISTRATOR) || $this->hasRole(RoleEnum::DEAN_OFFICE_WORKER);
    }

    public function canBeImpersonated(): bool
    {
        return !$this->hasRole(RoleEnum::ADMINISTRATOR) && !$this->hasRole(RoleEnum::DEAN_OFFICE_WORKER);
    }

    public function semesterConsultations(): HasMany
    {
        return $this->hasMany(SemesterConsultation::class, 'scientific_worker_id');
    }

    public function sessionConsultations(): HasMany
    {
        return $this->hasMany(SessionConsultation::class, 'scientific_worker_id');
    }

    public function partTimeConsultations(): HasMany
    {
        return $this->hasMany(PartTimeConsultation::class, 'scientific_worker_id');
    }

    public function desiderata(): HasMany
    {
        return $this->hasMany(Desideratum::class, 'scientific_worker_id');
    }
}
