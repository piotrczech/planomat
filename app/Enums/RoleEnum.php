<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: int
{
    case ADMIN = 1;
    case SCIENTIFIC_WORKER = 2;
    case STUDENT = 3;
    case TEACHER = 4;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::SCIENTIFIC_WORKER => 'Pracownik naukowy',
            self::STUDENT => 'Student',
            self::TEACHER => 'Nauczyciel',
        };
    }

    /**
     * Get all available roles
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
