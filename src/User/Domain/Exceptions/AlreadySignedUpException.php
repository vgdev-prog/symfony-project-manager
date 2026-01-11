<?php

declare(strict_types=1);

namespace App\User\Domain\Exceptions;

use App\Shared\Domain\Exception\AbstractDomainException;

final class AlreadySignedUpException extends AbstractDomainException
{
    public function __construct(string $message = 'User already signed up.')
    {
        parent::__construct($message);
    }


    public static function getDomainErrorCode(): string
    {
        return ErrorCode::USER_ALREADY_EXISTS->value;
    }
}
