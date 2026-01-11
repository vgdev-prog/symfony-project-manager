<?php

declare(strict_types=1);

namespace App\Model\Shared\Infrastructure\Service;

use App\Model\Shared\Domain\Contracts\FlusherInterface;
use Doctrine\ORM\EntityManagerInterface;

class Flusher implements FlusherInterface
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
