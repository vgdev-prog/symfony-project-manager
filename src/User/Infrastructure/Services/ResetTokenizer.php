<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Services;

use App\User\Domain\Contract\ResetTokenizerInterface;
use App\User\Domain\ValueObject\ResetToken;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final readonly class ResetTokenizer implements ResetTokenizerInterface
{
    public function __construct(
        private DateInterval $interval,
    )
    {
    }

    public function generate(DateTimeImmutable $now): ResetToken
    {
        return new ResetToken(
            token: Uuid::uuid4()->toString(),
            expiresAt: $now->add($this->interval),
        );
    }

}
