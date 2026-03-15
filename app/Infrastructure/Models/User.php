<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use App\Domain\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'academic_title',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function initials(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);

        return collect($parts)
            ->map(fn (string $namePart) => Str::of($namePart)->substr(0, 1))
            ->implode('');
    }

    public function academicTitle(): BelongsTo
    {
        return $this->belongsTo(AcademicTitle::class, 'academic_title', 'title');
    }

    public function fullName(bool $withTitle = true): string
    {
        $name = mb_trim(implode(' ', array_filter([$this->first_name, $this->last_name])));

        if (!$withTitle) {
            return $name;
        }

        $title = $this->academic_title;

        return $title ? mb_trim($title . ' ' . $name) : $name;
    }

    public function getNameAttribute(): string
    {
        return $this->fullName();
    }

    public function hasRole(RoleEnum $role): bool
    {
        return $this->role === $role;
    }

    public function isArchived(): bool
    {
        return $this->trashed();
    }

    public function canBeRestoredWithinWindow(): bool
    {
        if (!$this->trashed() || !$this->deleted_at) {
            return false;
        }

        return $this->deleted_at->greaterThanOrEqualTo(now()->subDay());
    }

    public function getIsRestoreAllowedAttribute(): bool
    {
        return $this->canBeRestoredWithinWindow();
    }

    public function getDisplayEmailAttribute(): string
    {
        return self::stripArchiveSuffixFromEmail($this->email);
    }

    public static function stripArchiveSuffixFromEmail(string $email): string
    {
        [$localPart, $domainPart] = self::splitEmail($email);
        $baseLocalPart = preg_replace('/\+archiwizacja\d+$/', '', $localPart) ?? $localPart;

        return $domainPart === ''
            ? $baseLocalPart
            : sprintf('%s@%s', $baseLocalPart, $domainPart);
    }

    public static function formatArchivedEmail(string $baseEmail, int $archiveIndex): string
    {
        [$localPart, $domainPart] = self::splitEmail($baseEmail);

        return $domainPart === ''
            ? sprintf('%s+archiwizacja%d', $localPart, $archiveIndex)
            : sprintf('%s+archiwizacja%d@%s', $localPart, $archiveIndex, $domainPart);
    }

    public function getArchivedAtFormattedAttribute(): ?string
    {
        return $this->deleted_at?->format('d.m.Y H:i');
    }

    public function reportIdentityLabel(): string
    {
        $label = sprintf('%s (%s)', $this->fullName(), $this->display_email);

        if ($this->isArchived()) {
            $archivedAt = $this->archived_at_formatted ?? '-';
            $archivedStatus = __('admin_settings.users.status.Archived At', ['date' => $archivedAt]);

            return sprintf('%s [%s]', $label, $archivedStatus);
        }

        if (!$this->is_active) {
            return sprintf('%s [%s]', $label, __('admin_settings.users.status.Suspended'));
        }

        return $label;
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

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitEmail(string $email): array
    {
        $parts = explode('@', $email, 2);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return [$email, ''];
        }

        return [$parts[0], $parts[1]];
    }
}
