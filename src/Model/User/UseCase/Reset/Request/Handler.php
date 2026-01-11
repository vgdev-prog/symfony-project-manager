<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Request;

use App\Model\Shared\Domain\Contracts\FlasherInterface;
use App\Model\User\Contracts\ResetTokenSenderInterface;
use App\Model\User\Contracts\UserRepositoryInterface;
use App\Model\User\Services\ResetTokenizer;
use App\Model\User\ValueObject\Email;

class Handler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private ResetTokenizer $tokenGenerator,
        private FlasherInterface $flasher,
        private ResetTokenSenderInterface $resetTokenSender,
    )
    {
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(Email::fromString($command->email));

        if (!$user) {
            throw new \DomainException('User not found');
        }

        $user->requestPasswordReset(
           $this->tokenGenerator->generate(),
            new \DateTimeImmutable()
        );

        $this->flasher->flush();
        $this->resetTokenSender->send();
    }

}
