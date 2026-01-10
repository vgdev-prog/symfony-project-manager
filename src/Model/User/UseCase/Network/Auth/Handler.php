<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use App\Model\User\Contracts\FlasherInterface;
use App\Model\User\Contracts\UserRepositoryInterface;
use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Id;
use DateTimeImmutable;
use DomainException;
use Exception;

class Handler
{
    public function __construct(
        private FlasherInterface        $flasher,
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        if ($this->userRepository->hasByNetworkIdentity($command->network,$command->identity)) {
            throw new DomainException('User already exists.');
        }

        $user = new User(
            Id::next(),
        new DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $command->network,
            $command->identity
        );

        $this->userRepository->add($user);

        $this->flasher->flush();
    }
}
