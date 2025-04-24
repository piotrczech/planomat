<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    Modules\Consultation\Providers\ConsultationServiceProvider::class,
    Modules\Desideratum\Providers\DesideratumServiceProvider::class,
];
