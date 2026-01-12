<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListeners;

use App\Shared\Domain\Exception\ResourceNotExistException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(priority: 10)]
final readonly class ResourceNotExistExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ResourceNotExistException) {
            return;
        }

        $event->setResponse(new JsonResponse(
            data: [
                'error' => [
                    'code' => $exception::getDomainErrorCode(),
                    'message' => $exception->getMessage(),
                    'context' => $exception->getPublicContext(),
                ],
            ],
            status: Response::HTTP_NOT_FOUND,
            headers: ['Content-Type' => 'application/problem+json']
        ));
    }

}
