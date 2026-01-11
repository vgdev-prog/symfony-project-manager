<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListeners;

use App\Shared\Domain\Exception\AbstractDomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class DomainExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private bool                     $isDebug = false,
    )
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AbstractDomainException) {
            return;
        }

        $statusCode = $exception->getStatusCode();
        $errorData = $this->buildErrorData($exception);

        $this->logException($exception, $statusCode);

        $response = new JsonResponse(
            data: ['error' => $errorData],
            status: $statusCode,
            headers: ['Content-Type' => 'application/problem+json'] // RFC 7807
        );

        $event->setResponse($response);
    }

    public function buildErrorData(AbstractDomainException $exception): array
    {
        $errorData = [
            'code' => $exception::getDomainErrorCode(),
            'message' => $exception->getMessage(),
        ];

        $context = $exception->getPublicContext();
        if (!empty($context)) {
            $errorData['context'] = $context;
        }

        if ($this->isDebug) {
            $errorData['debug'] = [
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->getTraceAsString($exception),
            ];
        }

        return $errorData;
    }

    private function logException(AbstractDomainException $exception, int $statusCode): void
    {
        $context = [
            'exception' => $exception,
            'code' => $exception::getDomainErrorCode(),
            'publicContext' => $exception->getPublicContext(),
        ];

        if ($statusCode >= 400 && $statusCode < 500) {
            $this->logger->error(
                message: 'Domain exception: {message}',
                context: array_merge(['message' => $exception->getMessage(), ...$context])
            );
        }
    }

    private function getTraceAsString(\Throwable $exception): array
    {
        return array_map(
            static fn(int $i, array $frame): string => sprintf(
                '#%d %s(%d): %s%s%s()',
                $i,
                $frame['file'] ?? '[internal]',
                $frame['line'] ?? 0,
                $frame['class'] ?? '',
                $frame['type'] ?? '',
                $frame['function'] ?? '',
            ),
            array_keys($exception->getTrace()),
            $exception->getTrace(),
        );

    }
}
