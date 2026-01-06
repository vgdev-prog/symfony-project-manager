<?php

declare(strict_types=1);

namespace App\Model\User\Services;

use App\Model\User\Contracts\PasswordHasherInterface;
use App\Model\User\Entity\User\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    )
    {
    }

    public function hash(string $password): string
    {
        return $this->passwordHasherFactory->getPasswordHasher(User::class)->hash($password);
    }
}
