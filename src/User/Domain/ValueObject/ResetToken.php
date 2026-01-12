<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use DomainException;

#[Embeddable]
final class ResetToken
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private readonly string            $token,

        #[ORM\Column(type: 'date_immutable', length: 255, nullable: true)]
        private readonly DateTimeImmutable $expiresAt,
    )
    {
        if (!$this->token) {
            throw new DomainException('Token cannot be empty.');
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expiresAt <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
