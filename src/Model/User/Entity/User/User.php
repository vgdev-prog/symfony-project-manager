<?php

namespace App\Model\User\Entity\User;


use App\Model\Shared\Domain\ValueObject\Id;
use App\Model\User\Enum\UserStatus;
use App\Model\User\Repository\UserRepository;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\ResetToken;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user_users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id                $id,

        #[ORM\Column(type: 'string', length: 255)]
        private DateTimeImmutable $date,

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?Email            $email,

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string           $password,

        #[ORM\Column(type: 'string', enumType: UserStatus::class)]
        private UserStatus        $userStatus,

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string           $confirmToken,

        #[ORM\Embedded(class: ResetToken::class, columnPrefix: 'reset_token')]
        private ?ResetToken       $resetToken,

        #[ORM\Column(type: 'string', length: 255)]
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
        return [];
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
        UserStatus        $userStatus,
        string            $token,
        ResetToken        $resetToken
    ): void
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
        $user->attachNetwork($network, $identity);
        $user->userStatus = UserStatus::ACTIVE;

        return $user;
    }

    /**
     * @throws Exception
     */
    private function attachNetwork(string $network, string $identity): void
    {
        /**
         * @var Network $existingNetwork
         */
        foreach ($this->networks as $existingNetwork) {
            if ($existingNetwork->isForNetwork($network)) {
                throw new DomainException('Network is already attached.');
            }
        }

        $this->networks->add(Network::fromNetwork($this, $network, $identity));
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

    public function confirmSignUp(): void
    {
        if ($this->isActive()) {
            throw new DomainException("User already confirmed.");
        }
        $this->userStatus = UserStatus::ACTIVE;
        $this->confirmToken = null;
    }


    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}
