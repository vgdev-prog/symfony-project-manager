<?php

declare(strict_types=1);

namespace App\Model\User\Exceptions;

enum ErrorCode: string
{
    case USER_ALREADY_EXISTS = 'USER_ALREADY_EXISTS';
}
