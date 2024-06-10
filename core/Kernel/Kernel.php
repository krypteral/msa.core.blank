<?php

declare(strict_types=1);

namespace Core\Kernel;

use Dotenv\Dotenv;

abstract class Kernel
{
    public function __construct()
    {
        if (!defined("ROOT")) {
            define("ROOT", realpath(dirname(__FILE__, 3)));
        }

        if (!defined("DS")) {
            define("DS", DIRECTORY_SEPARATOR);
        }

        if (!defined("BASE_DIR")) {
            define("BASE_DIR", ROOT . DS);
        }

        if (!defined("OS")) {
            define("OS", strtolower(substr(PHP_OS, 0, 3)));
        }

        (Dotenv::createUnsafeImmutable(ROOT))->load();

        require_once __DIR__ . "/../../bootstrap/helpers.php";
    }

    /**
     * @param array $argv
     * @return void
     */
    abstract public function run(array $argv = []): void;
}
