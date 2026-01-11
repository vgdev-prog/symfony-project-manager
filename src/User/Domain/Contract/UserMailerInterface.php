<?php

declare(strict_types=1);

namespace App\User\Domain\Contract;

use App\Shared\Domain\ValueObject\Email;

interface UserMailerInterface
{
    public function sendConfirmation(Email $email, string $token): void;

    public function resetToken(Email $email): void;
}
