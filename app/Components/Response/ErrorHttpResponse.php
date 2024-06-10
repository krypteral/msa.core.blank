<?php

declare(strict_types=1);

namespace Application\Components\Response;

class ErrorHttpResponse extends HttpResponse
{
    public function __construct($code = 404, $body = null)
    {
        parent::__construct($code, $body);
    }
}
