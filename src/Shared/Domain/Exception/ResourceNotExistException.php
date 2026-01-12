<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ResourceNotExistException extends ResourceNotFoundException
{
    public function __construct(string $message) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return 404;
    }

    public static function getDomainErrorCode(): string
    {
        return ErrorCode::RESOURCE_NOT_FOUND->value;
    }

}
