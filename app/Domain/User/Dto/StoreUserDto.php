<?php

declare(strict_types=1);

namespace App\Domain\User\Dto;

use App\Enums\RoleEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Rule; // For custom rules or direct rule objects
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Unique;
// For unique rule etc.
use Spatie\LaravelData\Attributes\Validation\Enum;

final class StoreUserDto extends Data
{
    public function __construct(
        #[Required]
        #[StringType]
        #[Rule('max:255')]
        public readonly string $name,
        #[Required]
        #[StringType]
        #[Email]
        #[Max(255)]
        #[Unique('users', 'email')]
        public readonly string $email,
        #[Required]
        #[StringType]
        #[Min(8)]
        #[Confirmed]
        public readonly string $password,

        // password_confirmation is automatically handled by Livewire/Laravel if password has Confirmed rule
        // public readonly string $password_confirmation,

        #[Required]
        #[Enum(RoleEnum::class)]
        public readonly RoleEnum $role = RoleEnum::SCIENTIFIC_WORKER, // Domyślna rola
    ) {
    }

    public static function messages(): array
    {
        return [
            'name.required' => __('admin_settings.users.validation.name_required'),
            'name.string' => __('admin_settings.users.validation.name_string'),
            'name.max' => __('admin_settings.users.validation.name_max'),
            'email.required' => __('admin_settings.users.validation.email_required'),
            'email.string' => __('admin_settings.users.validation.email_string'),
            'email.email' => __('admin_settings.users.validation.email_email'),
            'email.max' => __('admin_settings.users.validation.email_max'),
            'email.unique' => __('admin_settings.users.validation.email_unique'),
            'password.required' => __('admin_settings.users.validation.password_required'),
            'password.string' => __('admin_settings.users.validation.password_string'),
            'password.min' => __('admin_settings.users.validation.password_min'),
            'password.confirmed' => __('admin_settings.users.validation.password_confirmed'),
            'role.required' => __('admin_settings.users.validation.role_required'),
            'role.enum' => __('admin_settings.users.validation.role_enum'),
        ];
    }
}
