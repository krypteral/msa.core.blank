<?php

declare(strict_types=1);

namespace Application\Components\File;

use Generator;

class File
{
    /**
     * @var array
     */
    protected static array $handles = [];

    /**
     * @var string
     */
    protected string $filename;

    /**
     * @var array
     */
    protected array $hs = [];

    /**
     * @var array
     */
    protected array $hs_cursor = [];

    /**
     * @var array
     */
    protected array $hs_eof_pos = [];

    private function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function __destruct()
    {
        foreach ($this->hs as $h) {
            fclose($h);
        }
    }

    /**
     * @param string $filename
     * @param bool $continue_on_not_existing
     * @return false|mixed
     */
    public function getHandle(string $filename, bool $continue_on_not_existing = false): mixed
    {
        if (!file_exists($filename)) {
            if (!$continue_on_not_existing) {
                return false;
            }
        } else {
            clearstatcache(true, $filename);
        }

        $key = md5($filename);

        if (empty(static::$handles[$key])) {
            static::$handles[$key] = new static($filename);
        }

        return static::$handles[$key];
    }

    /**
     * @return Generator
     */
    public function iterate(): Generator
    {
        if (empty($this->hs["r"])) {
            $this->hs["r"] = fopen($this->filename, "r");

            fseek($this->hs["r"], 0, SEEK_END);

            $this->hs_eof_pos["r"] = ftell($this->hs["r"]);

            fseek($this->hs["r"], 0);
        }

        while (($line = fgets($this->hs["r"])) !== false) {
            $this->hs_cursor["r"] = ftell($this->hs["r"]);

            yield trim($line);
        }

        fseek($this->hs["r"], 0);
    }

    /**
     * @return void
     */
    public function deleteIfEOF(): void
    {
        if ($this->hs_eof_pos["r"] === $this->hs_cursor["r"]) {
            unlink($this->filename);
        }
    }

    /**
     * @param string $line
     * @return false|int
     */
    public function write(string $line): false|int
    {
        if (empty($this->hs["a"])) {
            $this->hs["a"] = fopen($this->filename, "a");
        }

        $result = fwrite($this->hs["a"], $line . PHP_EOL);

        $this->hs_cursor["a"] = ftell($this->hs["a"]);

        return $result;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if (!empty($this->hs["a"])) {
            $h = fopen($this->filename, "w");

            fclose($h);
        }

        if (!empty($this->hs["r"])) {
            $h = fopen($this->filename, "w");

            fclose($h);
        }
    }
}
