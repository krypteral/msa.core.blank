<?php

declare(strict_types=1);

namespace Core\Kernel;

use Application\Components\Logger\KernelCliLogger;

class Cli extends Kernel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(array $argv = []): void
    {
        try {
            require_once __DIR__ . "/../../bootstrap/cli.php";
        } catch (\Throwable $throwable) {
            (new KernelCliLogger())->error(jTraceEx($throwable));
        }
    }
}
