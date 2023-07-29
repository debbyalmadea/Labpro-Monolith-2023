<?php

namespace App\Exceptions;

use RuntimeException;

class HttpCustomException extends RuntimeException
{
    public function __construct(
        int $statusCode,
        string $message = "",
        private string $redirect = '',
        \Throwable|null $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getRedirect()
    {
        return $this->redirect;
    }
}