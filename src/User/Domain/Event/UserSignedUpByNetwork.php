<?php

declare(strict_types=1);

namespace App\User\Domain\Event;

use App\Shared\Domain\Event\AbstractDomainEvent;

class UserSignedUpByNetwork extends AbstractDomainEvent
{
    public function __construct(
        private readonly string  $userId,
        private readonly string  $network,
        private readonly string  $identity,
        private readonly ?string $email = null,
    )
    {
        parent::__construct();
    }

    public function aggregateId(): string
    {
        return $this->userId;
    }

    public function getUserId():string
    {
        return $this->userId;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'email' => $this->email,
            'network' => $this->network,
            'identity' => $this->identity,
        ];
    }


}
