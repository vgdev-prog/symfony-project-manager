<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Domain\Contract\DomainEventDispatcherInterface;
use App\Shared\Domain\Contract\FlusherInterface;
use App\Shared\Domain\ValueObject\Id;
use App\User\Application\Command\Input\SignUpByNetworkCommand;
use App\User\Domain\Contract\UserRepositoryInterface;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use DomainException;
use Exception;

readonly class SignUpByNetworkHandler
{
    public function __construct(
        private FlusherInterface $flusher,
        private UserRepositoryInterface $userRepository,
        private DomainEventDispatcherInterface $domainEventDispatcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(SignUpByNetworkCommand $command): void
    {
        if ($this->userRepository->hasByNetworkIdentity($command->network, $command->identity)) {
            throw new DomainException('User already exists.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $command->network,
            $command->identity
        );

        $this->userRepository->add($user);

        $this->flusher->flush();

        $this->domainEventDispatcher->dispatch($user);
    }
}
