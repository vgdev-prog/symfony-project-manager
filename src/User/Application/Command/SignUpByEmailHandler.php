<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Domain\Contract\DomainEventDispatcherInterface;
use App\Shared\Domain\Contract\FlusherInterface;
use App\Shared\Domain\Contract\TokenGeneratorInterface;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Id;
use App\User\Application\Command\Input\SignUpByEmailCommand;
use App\User\Domain\Contract\PasswordHasherInterface;
use App\User\Domain\Contract\UserMailerInterface;
use App\User\Domain\Contract\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use DomainException;

readonly class SignUpByEmailHandler
{
    public function __construct(
        private UserRepositoryInterface        $userRepository,
        private PasswordHasherInterface        $hasher,
        private TokenGeneratorInterface        $tokenGenerator,
        private DomainEventDispatcherInterface $domainEventDispatcher,
        private UserMailerInterface            $sender,
        private FlusherInterface               $flusher
    )
    {
    }

    public function handle(SignUpByEmailCommand $command): void
    {
        $mail = Email::fromString($command->email);

        if ($this->userRepository->findOneBy(['email' => $mail])) {
            throw new DomainException('Email already exists');
        }

        $token = $this->tokenGenerator->generate();

        $user = User::signUpByEmail(
            id: Id::next(),
            date: new DateTimeImmutable(),
            email: $mail,
            hash: $this->hasher->hash($command->password),
            token: $token
        );

        $this->userRepository->add($user);

        $this->flusher->flush();

        $this->domainEventDispatcher->dispatch($user);
    }

}
