<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Illuminate\Auth\AuthenticationException;

final class SuspendedAccountException extends AuthenticationException
{
}
