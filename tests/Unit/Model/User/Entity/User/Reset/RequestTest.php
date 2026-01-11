<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\Shared\Domain\ValueObject\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\ResetToken;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now);

        $user = $this->buildSignedByEmailUser();
        $user->confirmSignUp();

        $user->requestPasswordReset($token, $now);

        $this->assertNotNull($user->getResetToken());
    }

    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildSignedByEmailUser();
        $user->confirmSignUp();

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Reset already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testNotActivatedUser(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $this->buildUser();

        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);

    }

    public function buildSignedByEmailUser()
    {
        $user = $this->buildUser();
        $user->signUpByEmail(
            Email::fromString('test@app.test'),
            'hash',
            'token'
        );

        return $user;
    }

    public function buildUser(): User
    {
        return new User(
            Id::next(),
            new \DateTimeImmutable()
        );
    }

}
