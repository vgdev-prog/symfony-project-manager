<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\User;
use App\Model\User\Enum\UserStatus;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\Id;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{

    public function testSuccess(): void
    {
        $user = $this->buildSignUpUser();

        $user->confirmSignUp();

        $this->assertFalse($user->isWait());
        $this->assertTrue($user->isActive());
        $this->assertNull($user->getConfirmToken());
    }

    public function testAlready():void
    {
        $user = $this->buildSignUpUser();

        $user->confirmSignUp();
        $this->expectExceptionMessage('User already confirmed.');
        $user->confirmSignUp();
    }

    private function buildSignUpUser(): User
    {
        $id = Id::next();
        $email = Email::fromString('test@app.test');
        $password = 'hash';
        $token = 'token';

        $user = new User(
            $id,
            new DateTimeImmutable()
        );

        $user->signUpByEmail(
            $email,
            $password,
            $token
        );

        return $user;
    }
}
