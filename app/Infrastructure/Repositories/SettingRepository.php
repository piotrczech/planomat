<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\SettingRepositoryInterface;
use App\Infrastructure\Models\Setting;
use Illuminate\Support\Collection;

final class SettingRepository implements SettingRepositoryInterface
{
    public function getSettings(array $keys): Collection
    {
        return Setting::whereIn('key', $keys)->pluck('value', 'key');
    }

    public function updateSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
