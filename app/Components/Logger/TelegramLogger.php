<?php

declare(strict_types=1);

namespace Application\Components\Logger;

class TelegramLogger extends Logger
{
    protected string $filepath = BASE_DIR . "logs" . DS . "telegram.log";
}
