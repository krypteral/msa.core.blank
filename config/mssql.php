<?php

declare(strict_types=1);

return [
    "default" => [
        "host" => getenv("MSSQL_HOST"),
        "port" => getenv("MSSQL_PORT"),
        "username" => getenv("MSSQL_USERNAME"),
        "password" => getenv("MSSQL_PASSWORD"),
        "dbname" => getenv("MSSQL_DATABASE"),
        "charset" => getenv("MSSQL_CHARSET")
    ],
];
