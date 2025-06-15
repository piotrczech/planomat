<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use App\Domain\Enums\ActivityLogActionEnum;
use App\Domain\Enums\ActivityLogModuleEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Rule;

final class StoreActivityLogDto extends Data
{
    public function __construct(
        #[Required]
        #[Rule('exists:users,id')]
        public readonly string $userId,
        #[Required]
        #[Enum(ActivityLogModuleEnum::class)]
        public readonly string $module,
        #[Required]
        #[Enum(ActivityLogActionEnum::class)]
        public readonly string $action,
    ) {
    }

    public static function messages(): array
    {
        return [
            'userId.required' => __('admin_settings.validation_name_required'),
            'userId.exists' => __('admin_settings.validation_name_exists'),
            'module.required' => __('admin_settings.validation_name_required'),
            'module.enum' => __('admin_settings.validation_name_enum'),
            'action.required' => __('admin_settings.validation_name_required'),
            'action.enum' => __('admin_settings.validation_name_enum'),
        ];
    }
}
