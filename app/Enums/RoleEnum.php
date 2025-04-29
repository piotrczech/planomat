<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: int
{
    case SCIENTIFIC_WORKER = 1;
    case DEAN_OFFICE_WORKER = 2;
    case ADMINISTRATOR = 3;

    /**
     * Get human readable role name
     */
    public function label(): string
    {
        return match ($this) {
            self::SCIENTIFIC_WORKER => __('roles.scientific_worker'),
            self::DEAN_OFFICE_WORKER => __('roles.dean_office_worker'),
            self::ADMINISTRATOR => __('roles.administrator'),
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
