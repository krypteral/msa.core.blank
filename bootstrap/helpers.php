<?php

declare(strict_types=1);

use Core\Container;

if (!function_exists("guidv4")) {
    function guidv4(): string
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists("camelToKebabCase")) {
    function camelToKebabCase(string $str): string
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1-$2', $str));
    }
}

if (!function_exists("kebabToCamelCase")) {
    function kebabToCamelCase(string $str): array|string
    {
        $str = str_replace(" ", "", ucwords(str_replace("-", " ", $str)));

        $str[0] = strtolower($str[0]);

        return $str;
    }
}

if (!function_exists("kebabToPascalCase")) {
    function kebabToPascalCase(string $str): array|string
    {
        return str_replace(" ", "", ucwords(str_replace("-", " ", $str)));
    }
}

if (!function_exists("turkishToEnglish")) {
    function turkishToEnglish(string $str): array|string
    {
        $turkish = ["İ", "Ğ", "Ü", "Ş", "Ö", "Ç", "ı", "i̇", "ğ", "ü", "ş", "ö", "ç"];
        $english = ["I", "G", "U", "S", "O", "C", "i", "i", "g", "u", "s", "o", "c"];

        return str_replace($turkish, $english, $str);
    }
}

if (!function_exists("mb_ucfirst")) {
    function mb_ucfirst(string $str, bool $force = false): string
    {
        return mb_strtoupper(mb_substr($str, 0, 1)) . ($force ? mb_strtolower(mb_substr($str, 1)) : mb_substr($str, 1));
    }
}

if (!function_exists("mb_lcfirst")) {
    function mb_lcfirst(string $str): string
    {
        return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
    }
}

if (!function_exists("jTraceEx")) {
    function jTraceEx($e, $seen = null): string
    {
        $starter = $seen ? "Caused by: " : "";
        $result = [];

        if (!$seen) {
            $seen = [];
        }

        $trace = $e->getTrace();
        $prev = $e->getPrevious();
        $result[] = sprintf("%s%s: %s", $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();

        while (true) {
            $current = "$file:$line";

            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(" ... %d more", count($trace) + 1);
                break;
            }

            $result[] = sprintf(
                " at %s%s%s (%s%s%s)",
                count($trace) && array_key_exists("class", $trace[0]) ? str_replace("\\", ".", $trace[0]["class"]) : "",
                count($trace) && array_key_exists("class", $trace[0]) && array_key_exists("function", $trace[0]) ? "." : "",
                count($trace) && array_key_exists("function", $trace[0]) ? str_replace("\\", ".", $trace[0]["function"]) : "(main)",
                $line === null ? $file : basename($file),
                $line === null ? "" : ":",
                $line === null ? "" : $line
            );

            if (is_array($seen)) {
                $seen[] = "$file:$line";
            }

            if (!count($trace)) {
                break;
            }

            $file = array_key_exists("file", $trace[0]) ? $trace[0]["file"] : "Unknown Source";
            $line = array_key_exists("file", $trace[0]) && array_key_exists("line", $trace[0]) && $trace[0]["line"] ? $trace[0]["line"] : null;

            array_shift($trace);
        }

        $result = implode(PHP_EOL, $result);

        if ($prev) {
            $result .= PHP_EOL . jTraceEx($prev, $seen);
        }

        return $result;
    }
}

if (!function_exists("app")) {
    function app(): Container
    {
        return Container::getInstance();
    }
}

if (!function_exists("config")) {
    function config(): object
    {
        return require __DIR__ ."/../config/config.php";
    }
}
