<?php

declare(strict_types=1);

return (object) [
    "mysql" => require BASE_DIR . "config" . DS . "mysql.php",
    "mssql" => require BASE_DIR . "config" . DS . "mssql.php",
    "telegram" => require BASE_DIR . "config" . DS . "telegram.php"
];
