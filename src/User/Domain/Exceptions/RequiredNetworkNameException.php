<?php

declare(strict_types=1);

namespace App\User\Domain\Exceptions;

use App\Shared\Domain\Exception\AbstractDomainException;

final class RequiredNetworkNameException extends AbstractDomainException
{
    public function __construct(string $message = 'Required network name is missing')
    {
        parent::__construct($message);
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::MISSING_REQUIRED_NETWORK_NAME->value;
    }
}
