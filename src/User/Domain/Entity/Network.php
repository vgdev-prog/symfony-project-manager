<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\ValueObject\Id;
use Exception;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users_user_networks')]
final class Network
{

    /**
     * @throws Exception
     */
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id $id,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'networks')]
        #[ORM\JoinColumn(name: 'user_id',nullable: false, onDelete: 'CASCADE')]
        private User  $user,

        #[ORM\Column(type: 'string', length: 255)]
        private string $network,

        #[ORM\Column(type: 'string', length: 255)]
        private string $identity
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
     * Getters
     */

    public function getId():Id
    {
        return $this->id;
    }
    public function getUser(): User
    {
        return $this->user;
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
