<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    Modules\Consultation\Infrastructure\Providers\ConsultationServiceProvider::class,
    Modules\Desiderata\Infrastructure\Providers\DesiderataServiceProvider::class,
];
