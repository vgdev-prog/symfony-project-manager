<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\User;
use App\Model\User\Enum\UserStatus;
use App\Model\User\ValueObject\Email;
use App\Model\User\ValueObject\Id;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RequestTest extends TestCase
{

    public function testSuccess():void
    {
        $id = Id::next();
        $email = Email::fromString('test@app.test');
        $password = 'hash';
        $date = new DateTimeImmutable();

        $user = new User(
            $id,
            $email,
            $password,
            $date,
            UserStatus::WAIT
        );

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($date, $user->getDate());
    }
}
