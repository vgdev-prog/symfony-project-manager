<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http\SignUp\Request;

use App\User\Application\Command\Input\SignUpByEmailCommand;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Invalid mail format')]
        public readonly string $email,
        #[Assert\NotBlank(message: 'Password is required')]
        #[Assert\Length(min: 6, minMessage: 'Password must be at least {{limit}} characters')]
        public readonly string $password
    )
    {
    }

    public function toCommand(): SignUpByEmailCommand
    {
        return new SignUpByEmailCommand($this->email, $this->password);
    }
}
