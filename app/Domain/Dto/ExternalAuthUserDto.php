<?php

declare(strict_types=1);

namespace App\Domain\Dto;

use Spatie\LaravelData\Data;

final class ExternalAuthUserDto extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
    ) {
    }

    public function fullName(): string
    {
        return mb_trim("{$this->firstName} {$this->lastName}");
    }
}
