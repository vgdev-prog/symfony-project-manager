<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Id;
use App\User\Domain\Enum\UserRole;
use App\User\Domain\Enum\UserStatus;
use App\User\Domain\Event\UserSignedUpByEmail;
use App\User\Domain\Event\UserSignedUpByNetwork;
use App\User\Domain\Exceptions\NetworkAlreadyAttached;
use App\User\Domain\ValueObject\ResetToken;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'user_users',uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'email', columns: ['email']),
])]
final class User extends AggregateRoot implements UserInterface, PasswordAuthenticatedUserInterface

{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id                $id,

        #[ORM\Column(type: 'date_immutable', length: 255)]
        private DateTimeImmutable $date,

        #[ORM\Column(type: 'email', length: 255, nullable: true)]
        private ?Email            $email,

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string           $password,

        #[ORM\Column(name: 'user_status', type: 'string', enumType: UserStatus::class)]
        private UserStatus        $userStatus,

        #[ORM\Column(name: 'user_role', type: 'string', length: 255, nullable: true)]
        private ?string           $confirmToken,

        #[ORM\Embedded(class: ResetToken::class, columnPrefix: 'reset_token_')]
        private ?ResetToken       $resetToken,

        #[ORM\OneToMany(targetEntity: Network::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
        private Collection        $networks = new ArrayCollection()
    )
    {
    }

    /**
     * Getters
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    private function isNew(): bool
    {
        return $this->userStatus === UserStatus::NEW;
    }


    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getEmail();
    }

    public function getRoles(): array
    {
        return [UserRole::ROLE_USER->value];
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }


    public static function signUpByEmail(
        Id                $id,
        DateTimeImmutable $date,
        Email             $email,
        string            $hash,
        string            $token,
        UserStatus        $userStatus = UserStatus::NEW,
        ?ResetToken       $resetToken = null
    ): self
    {
        $user = new self(
            id: $id,
            date: $date,
            email: $email,
            password: $hash,
            userStatus: $userStatus,
            confirmToken: $token,
            resetToken: $resetToken
        );

        if (!$user->isNew()) {
            throw new DomainException('User is already signed up.');
        }
        $event = new UserSignedUpByEmail(
            $user->getId()->getValue(),
            $user->getEmail()->getValue()
        );

        $user->recordEvent($event);

        return $user;
    }

    /**
     * @throws Exception
     */
    public static function signUpByNetwork(
        Id                $id,
        DateTimeImmutable $date,
        string            $network,
        string            $identity,
        Email             $email = null,
    ): self
    {
        $user = new self(
            id: $id,
            date: $date,
            email: $email,
            password: null,
            userStatus: UserStatus::NEW,
            confirmToken: null,
            resetToken: null,
        );


        if (!$user->isNew()) {
            throw new DomainException('User is already signed up.');
        }

        $event = new UserSignedUpByNetwork(
            $id->getValue(),
            $network,
            $identity,
            $email?->getValue(),
        );

        $user->attachNetwork($network, $identity);
        $user->userStatus = UserStatus::ACTIVE;

        $user->recordEvent($event);

        return $user;
    }

    /**
     * @throws NetworkAlreadyAttached
     */
    private function attachNetwork(string $network, string $identity): void
    {
        /**
         * @var Network $existingNetwork
         */
        foreach ($this->networks as $existingNetwork) {
            if ($existingNetwork->isForNetwork($network)) {
                throw new NetworkAlreadyAttached($network);
            }
        }

        $this->networks->add(Network::fromNetwork($this, $network, $identity));
    }

    public function confirmSignUp(): void
    {
        if ($this->isActive()) {
            throw new DomainException("User already confirmed.");
        }
        $this->userStatus = UserStatus::ACTIVE;
        $this->confirmToken = null;
    }

    public function upgradePasswordHash(string $plainPassword): void
    {
        $this->password = $plainPassword;
    }

    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $now): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }

        if ($this->resetToken !== null && !$this->resetToken->isExpiredTo($now)) {
            throw new DomainException('Reset already requested.');
        }

        $this->resetToken = $token;
    }


    public function isActive(): bool
    {
        return $this->userStatus === UserStatus::ACTIVE;
    }

    public function isWait(): bool
    {
        return $this->userStatus === UserStatus::WAIT;
    }

    #[ORM\PostLoad()]
    public function checkEmbeds()
    {
        if (!$this->resetToken->getToken()) {
            $this->resetToken = null;
        }

    }


    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}
