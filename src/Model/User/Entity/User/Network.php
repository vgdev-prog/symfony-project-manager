<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\User\ValueObject\Id;
use Exception;

final readonly class Network
{
    /**
     * @throws Exception
     */
    private Id $id;

    private function __construct(
        private User  $user,
        public string $network,
        public string $identity
    )
    {
        $this->id = Id::next();
        if (!$this->network) {
            throw new Exception('Network name is required.');
        }
        if (!$this->identity) {
            throw new Exception('Network identity is required.');
        }
    }

    /**
     * @throws Exception
     */
    public static function fromNetwork(User $user, string $network, string $identity): self
    {
        return new self(
            user: $user,
            network: $network,
            identity: $identity
        );
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
