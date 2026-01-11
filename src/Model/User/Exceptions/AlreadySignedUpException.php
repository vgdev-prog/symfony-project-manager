<?php

declare(strict_types=1);

namespace App\Model\User\Exceptions;

use DomainException;

class AlreadySignedUpException extends DomainException
{
    private int $statusCode = 400;

    public function __construct(string $message = 'User already signed up.')
    {
        parent::__construct($message);
    }



}
