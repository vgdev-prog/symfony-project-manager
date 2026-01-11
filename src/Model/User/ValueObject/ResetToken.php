<?php

declare(strict_types=1);

namespace App\Model\User\ValueObject;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use DomainException;

#[Embeddable]
class ResetToken
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string            $token,

        #[ORM\Column(type: 'date_immutable', length: 255)]
        private DateTimeImmutable $expiresAt,
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
