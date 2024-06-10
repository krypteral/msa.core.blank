<?php

declare(strict_types=1);

namespace Core\Kernel;

use Application\Components\Logger\KernelTelegramLogger;
use Application\Components\Telegram\Telegram as ComponentTelegram;

class Telegram extends Kernel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(array $argv = []): void
    {
        try {
            $inbound_arguments = $argv;

            array_shift($inbound_arguments);

            $className = array_shift($inbound_arguments);
            $className = base64_decode($className);
            $method = array_shift($inbound_arguments);
            $data = array_shift($inbound_arguments);

            if ($className === ComponentTelegram::class && method_exists(ComponentTelegram::class, $method)) {
                (new $className())->$method($data);
            }
        } catch (\Throwable $throwable) {
            (new KernelTelegramLogger())->error(jTraceEx($throwable));
        }
    }
}
