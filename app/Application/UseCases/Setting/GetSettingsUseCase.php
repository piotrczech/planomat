<?php

declare(strict_types=1);

namespace App\Application\UseCases\Setting;

use App\Domain\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Collection;

final readonly class GetSettingsUseCase
{
    public function __construct(private SettingRepositoryInterface $settingRepository)
    {
    }

    public function execute(array $keys): Collection
    {
        return $this->settingRepository->getSettings($keys);
    }
}
