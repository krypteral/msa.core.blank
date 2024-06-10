<?php

declare(strict_types=1);

namespace Core\Kernel;

use Application\Components\Response\ErrorHttpResponse;

class Http extends Kernel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(array $argv = []): void
    {
        try {
            require_once __DIR__ . "/../../bootstrap/http.php";
        } catch (\Throwable $throwable) {
            (new ErrorHttpResponse($throwable->getCode(), $throwable->getMessage()))->flush();
        }
    }
}
