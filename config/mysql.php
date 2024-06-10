<?php

declare(strict_types=1);

return [
    "default" => [
        "host" => getenv("MYSQL_HOST"),
        "port" => intval(getenv("MYSQL_PORT")),
        "username" => getenv("MYSQL_USERNAME"),
        "password" => getenv("MYSQL_PASSWORD"),
        "dbname" => getenv("MYSQL_DATABASE"),
        "charset" => getenv("MYSQL_CHARSET")
    ],
];
