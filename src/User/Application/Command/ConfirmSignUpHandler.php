<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\Shared\Domain\Contract\FlusherInterface;
use App\User\Application\Command\Input\ConfirmSignUpCommand;
use App\User\Domain\Contract\UserRepositoryInterface;
use App\User\Domain\Exceptions\IncorrectTokenException;

readonly class ConfirmSignUpHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FlusherInterface $flusher
    )
    {
    }

    public function handle(ConfirmSignUpCommand $command): void
    {

        if (!$user = $this->userRepository->findByConfirmToken($command->token)) {
            throw new IncorrectTokenException();
        }

        $user->confirmSignUp();
        $this->flusher->flush();
    }

}
