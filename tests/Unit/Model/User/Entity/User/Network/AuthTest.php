<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Id;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess()
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'network',
            $identity = 'identity'
        );

        $this->assertTrue($user->isActive());

        $this->assertCount(1,$networks = $user->getNetworks());
        $this->assertInstanceOf(Network::class, $first = reset($networks));
        $this->assertEquals($network, $first->getNetwork());
        $this->assertEquals($identity, $first->getIdentity());
    }

    public function testAlready()
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'facebook',
            $identity ='111111'
        );

        $this->expectExceptionMessage('User is already signed up');

        $user->signUpByNetwork(
            $network,
            $identity
        );
    }
}
