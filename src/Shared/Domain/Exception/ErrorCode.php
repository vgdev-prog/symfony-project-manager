<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

enum ErrorCode:string
{
    case INVALID_EMAIL_FORMAT = 'INVALID_EMAIL_FORMAT';
    case INVALID_UUID_FORMAT = 'INVALID_UUID_FORMAT';

    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
}
