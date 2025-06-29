<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use Illuminate\Support\Collection;

interface SettingRepositoryInterface
{
    public function getSettings(array $keys): Collection;

    public function updateSettings(array $settings): void;
}
