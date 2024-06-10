<?php

declare(strict_types=1);

namespace Application\Components\Response;

class OkHttpResponse extends HttpResponse
{
    public function __construct($code = 200, $body = null)
    {
        parent::__construct($code, $body);
    }
}
