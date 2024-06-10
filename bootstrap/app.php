<?php

declare(strict_types=1);

use Application\Components\Database\MssqlDatabase;
use Application\Components\Database\MysqlDatabase;
use Application\Components\Logger\TelegramLogger;
use Core\App;
use Core\Kernel\Cli;
use Core\Kernel\Http;

$app = new App();

$app->setShared("kernel.http", function () {
    return new Http();
});

$app->setShared("kernel.cli", function () {
    return new Cli();
});

$app->setShared("kernel.telegram", function () {
    return new \Core\Kernel\Telegram();
});

$app->setShared("telegram", function () {
    return new \Application\Components\Telegram\Telegram();
});

$app->setShared("logger.telegram", function () {
    return new TelegramLogger();
});

$app->setShared("mysql.default", function () {
    return new MysqlDatabase();
});

$app->setShared("mssql.default", function () {
    return new MssqlDatabase();
});

$app->setShared("curl.default", function () {
    return new Application\Components\Curl\Curl();
});

return $app;
