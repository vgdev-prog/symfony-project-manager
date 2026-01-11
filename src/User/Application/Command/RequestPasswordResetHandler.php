<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Domain\Contract\FlusherInterface;
use App\Shared\Domain\ValueObject\Email;
use App\User\Application\Command\Input\RequestPasswordResetCommand;
use App\User\Domain\Contract\ResetTokenizerInterface;
use App\User\Domain\Contract\UserMailerInterface;
use App\User\Domain\Contract\UserRepositoryInterface;
use DateTimeImmutable;

readonly class RequestPasswordResetHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private ResetTokenizerInterface $tokenGenerator,
        private FlusherInterface $flusher,
        private UserMailerInterface $mailer,
    )
    {
    }

    public function handle(RequestPasswordResetCommand $command): void
    {
        $user = $this->users->getByEmail(Email::fromString($command->email));

        if (!$user) {
            throw new \DomainException('User not found');
        }

        $user->requestPasswordReset(
            $this->tokenGenerator->generate(new DateTimeImmutable()),
            new DateTimeImmutable()
        );

        $this->flusher->flush();
    }

}
