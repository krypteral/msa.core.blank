<?php

declare(strict_types=1);

if (!defined("START_TIME")) {
    define("START_TIME", microtime(true));
}

require_once __DIR__ . "/../vendor/autoload.php";

$app = require_once __DIR__ . "/../bootstrap/app.php";

$kernel = $app->getShared("kernel.cli");

$kernel->run($argv);
