<?php

declare(strict_types=1);

namespace App\User\Domain\Exceptions;

use App\Shared\Domain\Exception\AbstractDomainException;

final class NetworkAlreadyAttached extends AbstractDomainException
{
    public function __construct(private string $network)
    {
        parent::__construct('Network already attached.');
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::MISSING_REQUIRED_NETWORK_NAME->value;
    }

    public function getPublicContext(): array
    {
        return [
            'network' => $this->network,
        ];
    }

    public static function getExamplePublicContext(): array
    {
        return [
            'network' => 'google'
        ];
    }
}
