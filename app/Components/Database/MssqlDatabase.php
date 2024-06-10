<?php

declare(strict_types=1);

namespace Application\Components\Database;

use PDO;
use PDOStatement;

class MssqlDatabase implements Database
{
    /**
     * @var PDO
     */
    public static PDO $connection;

    /**
     * @var false|PDOStatement
     */
    private false|PDOStatement $pdo_statement;

    public function __construct($config = null)
    {
        if (empty(self::$connection)) {
            if (empty($config)) {
                $config = config()->mssql["default"];
            }

            $dsn = [
                "win" => "sqlsrv:server=%s,%s;Database=%s;Encrypt=No",
                "lin" => "dblib:host=%s:%s;dbname=%s"
            ];

            $connection = new PDO(
                vsprintf($dsn[OS], [
                    $config["host"],
                    $config["port"],
                    $config["dbname"]
                ]),
                $config["username"],
                $config["password"]
            );

            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $connection->query("SET ANSI_NULLS, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");

            self::$connection = $connection;
        }
    }

    public function startTransaction()
    {
        return self::$connection->beginTransaction();
    }

    public function endTransaction(): bool
    {
        return self::$connection->commit();
    }

    public function query(string $query): false|PDOStatement
    {
        $this->pdo_statement = self::$connection->query($query);

        return $this->pdo_statement;
    }

    public function lastInsertId(): false|string
    {
        return self::$connection->lastInsertId();
    }

    public function affectedRows(): int|string
    {
        if (!$this->pdo_statement) {
            throw new \PDOException("no pdo_statement");
        }

        return $this->pdo_statement->rowCount();
    }

    public function fetchOne(string $query): mixed
    {
        $this->pdo_statement = $this->query($query);

        return $this->pdo_statement->fetch();
    }

    public function fetchAll($query): \Generator
    {
        $this->pdo_statement = $this->query($query);

        while ($row = $this->pdo_statement->fetch()) {
            yield $row;
        }
    }

    public function escapeString(string $str = null): false|string
    {
        return self::$connection->quote($str);
    }

    public function escapeIdentifier(mixed $str): string
    {
        return '`' . self::$connection->quote((string)$str) . '`';
    }

    public function escapeBinary(?string $str): string
    {
        if ($str === null || !ctype_xdigit(ltrim($str, "0x"))) {
            return "NULL";
        }

        return $str;
    }
}
