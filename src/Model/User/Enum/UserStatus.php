<?php

declare(strict_types=1);

namespace App\Model\User\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case WAIT = 'wait';
    case INACTIVE = 'inactive';
}
