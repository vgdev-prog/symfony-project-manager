<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use App\Model\Shared\Domain\ValueObject\Id;
use Exception;

final class Network
{
    /**
     * @throws Exception
     */

    private function __construct(
        private Id $id,
        private User  $user,
        public string $network,
        public string $identity
    )
    {
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
            id: Id::next(),
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
