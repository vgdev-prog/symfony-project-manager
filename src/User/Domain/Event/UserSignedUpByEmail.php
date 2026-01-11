<?php

declare(strict_types=1);

namespace App\User\Domain\Event;

use App\Shared\Domain\Event\AbstractDomainEvent;

class UserSignedUpByEmail extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $userId,
        private readonly string $email,
    )
    {
        parent::__construct();
    }

    public function aggregateId(): string
    {
        return $this->userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getEmail():string
    {
        return $this->email;
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'email' => $this->email,
        ];
    }
}
