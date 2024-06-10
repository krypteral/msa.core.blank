<?php

declare(strict_types=1);

namespace Application\Components\Logger;

class KernelCliLogger extends Logger
{
    protected string $filepath = BASE_DIR . "logs" . DS . "kernelCli.log";
}
