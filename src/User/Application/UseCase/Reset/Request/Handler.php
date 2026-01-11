<?php

declare(strict_types=1);

namespace App\User\Application\UseCase\Reset\Request;

use App\Shared\Domain\Contract\FlusherInterface;
use App\Shared\Domain\Contract\TokenGeneratorInterface;
use App\Shared\Domain\ValueObject\Email;
use App\User\Domain\Contract\ResetTokenSenderInterface;
use App\User\Domain\Contract\UserRepositoryInterface;
class Handler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private TokenGeneratorInterface $tokenGenerator,
        private FlusherInterface $flusher,
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

        $this->flusher->flush();
        $this->resetTokenSender->send();
    }

}
