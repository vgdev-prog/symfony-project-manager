<?php

declare(strict_types=1);

namespace App\User\Domain\Contract;

use App\Shared\Domain\ValueObject\Email;
use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function findByConfirmToken(string $token): ?User;

    public function hasByNetworkIdentity(string $network, string $identity): bool;

    public function getByEmail(Email $email):?User;

    public function hasByMail(Email $email): bool;

}
