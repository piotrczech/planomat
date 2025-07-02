<?php

declare(strict_types=1);

return [
    App\Infrastructure\Providers\AppServiceProvider::class,
    App\Infrastructure\Providers\VoltServiceProvider::class,
    Modules\Consultation\Infrastructure\Providers\ConsultationServiceProvider::class,
    Modules\Desiderata\Infrastructure\Providers\DesiderataServiceProvider::class,
    Lab404\Impersonate\ImpersonateServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
];
