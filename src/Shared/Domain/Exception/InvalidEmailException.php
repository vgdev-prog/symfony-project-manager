<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidEmailException extends AbstractDomainException
{
    public function __construct(private string $value)
    {
        parent::__construct(sprintf('"%s" is not a valid Email.', $value));
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::INVALID_EMAIL_FORMAT->value;
    }

    public function getPublicContext(): array
    {
        return [
            'email' => $this->value,
        ];
    }

    public static function getExamplePublicContext():array
    {
        return [
            'email' => 'user@mail.com',
        ];
    }
}
