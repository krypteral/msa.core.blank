<?php

declare(strict_types=1);

namespace Application\Components\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

abstract class Logger
{
    /**
     * @var string
     */
    protected string $filepath;

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::EMERGENCY . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::ALERT . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::CRITICAL . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::ERROR . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::WARNING . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::NOTICE . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::INFO . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        file_put_contents(
            $this->filepath,
            date("Y-m-d H:i:s") . " " . LogLevel::DEBUG . ": " . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }
}
