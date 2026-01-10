<?php

namespace App\Model\User\Entity\User;


use AllowDynamicProperties;
use App\Model\User\Enum\UserStatus;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\Id;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainException;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface

{

    private Email $email;
    private ?string $password = null;
    private ?string $confirmToken = null;
    private Collection $networks;

    public function __construct(
        private Id                $id,
        private DateTimeInterface $date,
        private UserStatus        $status = UserStatus::NEW,
    )
    {
        $this->networks = new ArrayCollection();
    }

    public function signUpByEmail(
        Email  $email,
        string $hash,
        string $token
    ): void
    {
        $this->email = $email;
        $this->password = $hash;
        $this->confirmToken = $token;
        $this->status = UserStatus::WAIT;
    }

    /**
     * @throws Exception
     */
    public function signUpByNetwork(
        string $network,
        string $identity,
    ): void
    {
        if (!$this->isNew()) {
            throw new DomainException('User is already signed up.');
        }
        $this->attachNetwork($network, $identity);
        $this->status = UserStatus::ACTIVE;
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

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    private function isNew(): bool
    {
        return $this->status === UserStatus::NEW;
    }


    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getEmail();
    }

    public function getRoles(): array
    {
        return $this->getRoles();
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function isWait(): bool
    {
        return $this->status === UserStatus::WAIT;
    }

    public function confirmSignUp(): void
    {
        if ($this->isActive()) {
            throw new DomainException("User already confirmed.");
        }
        $this->status = UserStatus::ACTIVE;
        $this->setConfirmToken(null);
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function setConfirmToken(?string $token): void
    {
        $this->confirmToken = $token;
    }


}
