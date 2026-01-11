<?php

declare(strict_types=1);

namespace App\Model\Shared\Domain\Contracts;

interface FlusherInterface
{
    public function flush():void;
}
