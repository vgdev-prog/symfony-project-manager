<?php

declare(strict_types=1);

namespace App\Model\User\Services;

use App\Model\User\Contracts\TokenGeneratorInterface;
use Random\RandomException;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
