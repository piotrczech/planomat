<?php

declare(strict_types=1);

namespace Modules\Consultation\Enums;

enum ConsultationType: string
{
    case Semester = 'semester';
    case Session = 'session';
}
