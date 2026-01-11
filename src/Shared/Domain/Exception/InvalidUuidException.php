<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidUuidException extends AbstractDomainException
{
    public function __construct(private readonly string $value)
    {
        parent::__construct(sprintf('"%s" is not a valid UUID.', $value));
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::INVALID_UUID_FORMAT->value;
    }

    public function getPublicContext(): array
    {
        return [
            'uuid' => $this->value,
        ];
    }
}
