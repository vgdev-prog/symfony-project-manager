<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\Id;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testSuccess(): void
    {
        $id = Id::next();
        $date = new DateTimeImmutable();
        $email = Email::fromString('test@app.test');
        $password = 'hash';
        $token = 'token';

        $user = new User(
            $id,
            $date
        );

        $user->signUpByEmail(
            $email,
            $password,
            $token
        );


        $this->assertTrue($user->isWait());
        $this->assertFalse($user->isActive());

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($date, $user->getDate());
    }
}
