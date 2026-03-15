<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserListViewEnum: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
