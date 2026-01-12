<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListeners;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
final readonly class ValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof UnprocessableEntityHttpException) {
            return;
        }

        $previous = $exception->getPrevious();
        if (!$previous instanceof ValidationFailedException) {
            return;
        }

        $errors = [];
        foreach ($previous->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $event->setResponse(new JsonResponse(
            data: [
                'error' => 'Validation failed',
                'violations' => $errors,
            ], status: Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
