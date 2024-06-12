<?php

declare(strict_types=1);

namespace Application\Components\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
