<?php

declare(strict_types=1);

namespace App\User\Domain\Contract;

use App\User\Domain\ValueObject\ResetToken;
use DateTimeImmutable;

interface ResetTokenizerInterface
{
    public function generate(DateTimeImmutable $now):ResetToken;
}
