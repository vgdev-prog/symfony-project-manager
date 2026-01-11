<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Shared\Domain\Contracts\FlasherInterface;
use App\Model\Shared\Domain\Contracts\TokenGeneratorInterface;
use App\Model\Shared\Domain\ValueObject\Id;
use App\Model\User\Contracts\PasswordHasherInterface;
use App\Model\User\Contracts\SignUpConfirmEmailSenderInterface;
use App\Model\User\Contracts\UserRepositoryInterface;
use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Email;
use DateTimeImmutable;
use DomainException;

readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface           $userRepository,
        private PasswordHasherInterface           $hasher,
        private TokenGeneratorInterface           $tokenGenerator,
        private SignUpConfirmEmailSenderInterface $sender,
        private FlasherInterface $flasher
    )
    {
    }

    public function handle(Command $command): void
    {
        $mail = Email::fromString($command->email);

        if ($this->userRepository->findOneBy(['email' => $mail])) {
            throw new DomainException('Email already exists');
        }

        $token = $this->tokenGenerator->generate();

       $user = new User(
           Id::next(),
           new DateTimeImmutable()
       );
       $user->signUpByEmail(
           $mail,
           $this->hasher->hash($command->password),
           $token
       );

        $this->userRepository->add($user);
        $this->flasher->flush();
        $this->sender->send($user->getEmail(), $token);

    }

}
