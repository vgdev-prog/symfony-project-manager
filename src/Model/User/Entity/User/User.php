<?php

namespace App\Model\User\Entity\User;




use App\Model\User\Enum\UserStatus;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\Id;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    public function __construct(
        private Id   $id,
        private Email $email,
        private string $password,
        private DateTimeInterface $date,
        private UserStatus $status
    )
    {
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

    public function getUserIdentifier(): string
    {
       return (string) $this->getEmail();
    }

    public function getRoles(): array
    {
        return $this->getRoles();
    }

    public function isActive():UserStatus
    {
       return $this->status = UserStatus::ACTIVE;
    }

    public function isWait():UserStatus
    {
        return $this->status = UserStatus::WAIT;
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


}
