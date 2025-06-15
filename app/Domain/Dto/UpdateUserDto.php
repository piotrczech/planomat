<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use App\Domain\Enums\RoleEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Illuminate\Validation\Rule as IlluminateRule;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateUserDto extends Data
{
    public function __construct(
        #[Required]
        public readonly int $id,
        #[Required]
        #[StringType]
        #[Rule('max:255')]
        public readonly string $name,
        #[Required]
        #[StringType]
        #[Email]
        #[Rule('max:255')]
        public readonly string $email,
        #[Sometimes]
        #[StringType]
        #[Min(8)]
        #[Confirmed]
        public readonly ?string $password,
        #[Required]
        #[Enum(RoleEnum::class)]
        public readonly RoleEnum $role,
        public string|Optional $password_confirmation,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        $userId = $context->payload['id'] ?? null;

        return [
            'id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                IlluminateRule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['nullable', 'string', new Min(8), 'confirmed'],
            'role' => ['required', new Enum(RoleEnum::class)],
        ];
    }

    public static function messages(): array
    {
        return [
            'id.required' => __('admin_settings.users.validation.id_required'),
            'id.integer' => __('admin_settings.users.validation.id_integer'),
            'id.exists' => __('admin_settings.users.validation.id_exists'),
            'name.required' => __('admin_settings.users.validation.name_required'),
            'name.string' => __('admin_settings.users.validation.name_string'),
            'name.max' => __('admin_settings.users.validation.name_max'),
            'email.required' => __('admin_settings.users.validation.email_required'),
            'email.string' => __('admin_settings.users.validation.email_string'),
            'email.email' => __('admin_settings.users.validation.email_email'),
            'email.max' => __('admin_settings.users.validation.email_max'),
            'email.unique' => __('admin_settings.users.validation.email_unique'),
            'password.string' => __('admin_settings.users.validation.password_string'),
            'password.min' => __('admin_settings.users.validation.password_min'),
            'password.confirmed' => __('admin_settings.users.validation.password_confirmed'),
            'role.required' => __('admin_settings.users.validation.role_required'),
            'role.enum' => __('admin_settings.users.validation.role_enum'),
        ];
    }
}
