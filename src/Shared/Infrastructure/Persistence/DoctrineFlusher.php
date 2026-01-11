<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Contract\FlusherInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFlusher implements FlusherInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
