<?php

declare(strict_types=1);

namespace App\User\Domain\Exceptions;

use App\Shared\Domain\Exception\AbstractDomainException;
use Throwable;

class UserAlreadyExist extends AbstractDomainException
{
    public function __construct(private string $email)
    {
        $message = 'User already exist';
        parent::__construct($message);
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::USER_ALREADY_EXIST->value;
    }

    public function getPublicContext(): array
    {
        return [
            'email' => $this->email,
        ];
    }

    public static function getExamplePublicContext(): array
    {
        return [
            'email' => 'user@example.com'
        ];
    }
}
