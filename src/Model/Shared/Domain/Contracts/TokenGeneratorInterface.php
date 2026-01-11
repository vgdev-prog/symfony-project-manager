<?php

declare(strict_types=1);

namespace App\Model\Shared\Domain\Contracts;

interface TokenGeneratorInterface
{
    public function generate():string;
}
