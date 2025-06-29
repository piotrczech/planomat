<?php

declare(strict_types=1);

namespace App\Application\UseCases\Setting;

use App\Domain\Interfaces\SettingRepositoryInterface;

final readonly class UpdateSettingsUseCase
{
    public function __construct(private SettingRepositoryInterface $settingRepository)
    {
    }

    public function execute(array $settings): void
    {
        $this->settingRepository->updateSettings($settings);
    }
}
