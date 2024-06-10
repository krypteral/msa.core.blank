<?php

declare(strict_types=1);

namespace Application\Components\Database;

use Generator;
use PDOStatement;

interface Database
{
    /**
     * @return void|bool
     */
    public function startTransaction();

    /**
     * @return void|bool
     */
    public function endTransaction();

    /**
     * @param string $query
     * @return int|string|false|PDOStatement
     */
    public function query(string $query): int|string|false|PDOStatement;

    /**
     * @return int|string|false
     */
    public function lastInsertId(): int|string|false;

    /**
     * @return int|string
     */
    public function affectedRows(): int|string;

    /**
     * @param string $query
     * @return mixed
     */
    public function fetchOne(string $query): mixed;

    /**
     * @param string $query
     * @return Generator
     */
    public function fetchAll(string $query): Generator;

    /**
     * @param string|null $str
     * @return false|string
     */
    public function escapeString(string $str = null): false|string;

    /**
     * @param mixed $str
     * @return string
     */
    public function escapeIdentifier(mixed $str): string;

    /**
     * @param string|null $str
     * @return string
     */
    public function escapeBinary(?string $str): string;
}
