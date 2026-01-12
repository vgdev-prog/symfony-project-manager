<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListeners;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

#[AsEventListener]
class UnsupportedMediaTypeListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof UnsupportedMediaTypeHttpException) {
            return;
        }

        $event->setResponse(new JsonResponse(
            data: [
                'error' => 'Invalid request format',
                'code' => 'UNSUPPORTED_MEDIA_TYPE',
                'message' => 'Content-Type must be application/json with valid JSON body',
            ],
            status: Response::HTTP_UNSUPPORTED_MEDIA_TYPE
        ));
    }

}
