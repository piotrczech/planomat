<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum ActivityLogActionEnum: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';

    public function label(): string
    {
        return match ($this) {
            self::CREATE => __('activity_log.action.create'),
            self::UPDATE => __('activity_log.action.update'),
            self::DELETE => __('activity_log.action.delete'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
