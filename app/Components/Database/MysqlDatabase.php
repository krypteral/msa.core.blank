<?php

declare(strict_types=1);

namespace Application\Components\Database;

use Exception;
use mysqli;

class MysqlDatabase implements Database
{
    /**
     * @var mysqli|false|null
     */
    public static mysqli|null|false $connection;

    public function __construct($config = null)
    {
        if (empty(self::$connection)) {
            if (empty($config)) {
                $config = config()->mysql["default"];
            }

            self::$connection = mysqli_connect(
                $config["host"],
                $config["username"],
                $config["password"],
                $config["dbname"],
                $config["port"]
            );

            if (!self::$connection) {
                throw new Exception(mysqli_connect_error());
            }

            self::$connection->set_charset($config["charset"]);
            self::$connection->set_opt(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        }
    }

    public function startTransaction(): void
    {
        self::$connection->autocommit(false);
    }

    public function endTransaction(): void
    {
        self::$connection->commit();
        self::$connection->autocommit(true);
    }

    public function query(string $query): int|string
    {
        $result = self::$connection->query($query);

        if (!$result) {
            throw new Exception(self::$connection->error, self::$connection->errno);
        }

        return $this->lastInsertId();
    }

    public function lastInsertId(): int|string
    {
        return self::$connection->insert_id;
    }

    public function affectedRows(): int|string
    {
        return self::$connection->affected_rows;
    }

    public function fetchOne(string $query): mixed
    {
        $r = self::$connection->query($query);

        $row = $r->fetch_array(MYSQLI_ASSOC);

        $r->free();

        return $row;
    }

    public function fetchAll(string $query): \Generator
    {
        $r = self::$connection->query($query);

        if ($r === false) {
            throw new Exception(mysqli_error(self::$connection), mysqli_errno(self::$connection));
        }

        while ($row = $r->fetch_array(MYSQLI_ASSOC)) {
            yield $row;
        }

        $r->free();
    }

    public function escapeString(mixed $str = null): false|string
    {
        return $str === null ? "NULL" : "'" . self::$connection->real_escape_string((string)$str) . "'";
    }

    public function escapeIdentifier(mixed $str): string
    {
        return '`' . self::$connection->real_escape_string((string)$str) . '`';
    }

    public function escapeBinary(?string $str): string
    {
        if ($str === null || !ctype_xdigit(ltrim($str, "0x"))) {
            return "NULL";
        }

        return $str;
    }
}
