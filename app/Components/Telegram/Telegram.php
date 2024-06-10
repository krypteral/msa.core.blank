<?php

declare(strict_types=1);

namespace Application\Components\Telegram;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Telegram
{
    /**
     * @var int
     */
    public static int $counter = 0;

    /**
     * @var bool
     */
    public static bool $isTest = false;

    /**
     * @var bool
     */
    public static bool $verbose = false;

    /**
     * @var bool
     */
    public static bool $enableLogging = false;

    /**
     * @var int
     */
    private static int $_unlockWaitTimeout = 60;

    /**
     * @var string
     */
    private static string $_lockFile = BASE_DIR . "cache" . DS . "Telegram.lock";

    /**
     * @var resource
     */
    private static $_lockFilePointer;

    /**
     * @var mixed
     */
    private static mixed $lastResult = false;

    /**
     * @return bool
     */
    public function locked(): bool
    {
        $counter = 0;
        $flockResult = null;

        self::$_lockFilePointer = fopen(self::$_lockFile, "w+");

        do {
            if ($counter > 0) {
                sleep(1);
            }

            if ($counter > self::$_unlockWaitTimeout) {
                break;
            }

            $counter++;

            $flockResult = flock(self::$_lockFilePointer, LOCK_EX | LOCK_NB);
        } while (!$flockResult);

        return $flockResult;
    }

    /**
     * @return void
     */
    public function unlock(): void
    {
        if (self::$_lockFilePointer) {
            flock(self::$_lockFilePointer, LOCK_UN);
            fclose(self::$_lockFilePointer);
        }
    }

    /**
     * @return bool
     */
    public function getLastResponse(): bool
    {
        return self::$lastResult;
    }

    /**
     * @param mixed $options
     * @param string $text_wrap_start_tag
     * @param string $text_wrap_end_tag
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return false
     */
    public function send(mixed $options, string $text_wrap_start_tag = "", string $text_wrap_end_tag = ""): bool
    {
        try {
            if (!is_array($options)) {
                $text = $options;
                $options = [];
                $options["text"] = $text;
                $options["text_wrap_start_tag"] = $text_wrap_start_tag;
                $options["text_wrap_end_tag"] = $text_wrap_end_tag;
            }

            if (empty($config) || !is_object($config)) {
                $config = config()->telegram;
            }

            $text = array_key_exists("text", $options) ? $options["text"] : null;
            $text_wrap_start_tag = array_key_exists("text_wrap_start_tag", $options) ? $options["text_wrap_start_tag"] : "";
            $text_wrap_end_tag = array_key_exists("text_wrap_end_tag", $options) ? $options["text_wrap_end_tag"] : "";
            $custom_field = array_key_exists("custom_field", $options) ? $options["custom_field"] : [];
            $filename = array_key_exists("filename", $options) ? $options["filename"] : null;
            $filetype = array_key_exists("filetype", $options) ? $options["filetype"] : null;
            $caption = array_key_exists("caption", $options) ? $options["caption"] : null;
            $reply_markup = array_key_exists("reply_markup", $options) ? $options["reply_markup"] : [];
            $chat_id = array_key_exists("chat_id", $options) ? $options["chat_id"] : $config["chat_id_info"];
            $key = array_key_exists("key", $options) ? $options["key"] : $config["key"];
            $verbose = array_key_exists("verbose", $options) ? $options["verbose"] : null;
            $parse_mode = array_key_exists("parse_mode", $options) ? $options["parse_mode"] : "HTML";
            $disable_web_page_preview = array_key_exists("disable_web_page_preview", $options) ? $options["disable_web_page_preview"] : true;

            if ($verbose) {
                self::$verbose = true;
            }

            $file = array_key_exists("debug", $options) && array_key_exists("file", $options["debug"]) ? $options["debug"]["file"] : null;
            $line = array_key_exists("debug", $options) && array_key_exists("line", $options["debug"]) ? $options["debug"]["line"] : null;

            if (!is_null($file) && !is_null($line)) {
                self::$enableLogging = true;
            }

            $a = [
                "chat_id" => $chat_id,
                "disable_web_page_preview" => $disable_web_page_preview
            ];

            if ($parse_mode) {
                $a = array_merge($a, ["parse_mode" => $parse_mode]);
            }

            if ($reply_markup) {
                $a = array_merge($a, ["reply_markup" => json_encode($reply_markup)]);
            }

            $url = "sendMessage";
            $header = "Content-type: application/x-www-form-urlencoded";

            switch ($filetype) {
                case "photo":
                    if (is_array($filename) && count($filename) > 1) {
                        $url = "sendMediaGroup";
                        $header = "Content-type: multipart/form-data";
                        $upload_file_type = "photo";
                        $media = [];

                        foreach ($filename as $k => $fn) {
                            $fname = $upload_file_type . "_" . $k;
                            $file = new \SplFileInfo($fn);
                            $fn = $file->getPathname();
                            $mime = mime_content_type($fn);
                            $a = array_merge($a, [$fname => new \CurlFile($fn, $mime, $fname)]);

                            $media[] = [
                                "type" => $upload_file_type,
                                "media" => "attach://" . $fname,
                                "caption" => $caption
                            ];
                        }

                        $a = array_merge($a, ["media" => json_encode($media)]);
                    } else {
                        $url = "sendPhoto";
                        $header = "Content-type: multipart/form-data";

                        if ($custom_field) {
                            $a = array_merge($a, $custom_field);
                        }

                        $filename = is_array($filename) ? current($filename) : $filename;
                        $file = new \SplFileInfo($filename);
                        $fullpath = $file->getPathname();
                        $fname = $file->getFileName();
                        $file = new \CurlFile($fullpath, mime_content_type($fullpath), $fname);
                        $a = array_merge($a, ["photo" => $file, "caption" => $caption]);
                    }

                    $this->_send($key, $url, $a, $header);

                    break;
                case "document":
                    $url = "sendDocument";
                    $header = "Content-type: multipart/form-data";

                    if ($custom_field) {
                        $a = array_merge($a, $custom_field);
                    }

                    $file = new \SplFileInfo($filename);
                    $fullpath = $file->getPathname();
                    $fname = $file->getFileName();
                    $file = new \CurlFile($fullpath, mime_content_type($fullpath), $fname);
                    $a = array_merge($a, ["document" => $file, "caption" => $caption]);

                    $this->_send($key, $url, $a, $header);

                    break;
                default:
                    $text = trim($text);

                    if ($text === "") {
                        return false;
                    }

                    if ($custom_field) {
                        $a = array_merge($a, $custom_field);
                    }

                    $strings = $this->chunk_split_unicode($text, 4010);

                    unset($text);

                    foreach ($strings as $fetched_text) {
                        $fetched_text = $text_wrap_start_tag . $fetched_text . $text_wrap_end_tag;
                        $a = array_merge($a, ["text" => $fetched_text]);
                        $a = http_build_query($a);

                        $this->_send($key, $url, $a, $header);
                    }

                    break;
            }
        } catch (\Exception $e) {
            app()->getShared("logger.telegram")->error(var_export($e->getMessage(), true));
        }

        return false;
    }

    /**
     * @param mixed $options
     * @return void
     */
    public function sendNonBlocking(mixed $options): void
    {
        if (!is_array($options)) {
            $options = [
                "text" => $options
            ];
        }

        $options["text"] = "<i>" . date("Y-m-d H:i:s") . "</i>: " . $options["text"];
        $path = (new \SplFileInfo(__FILE__))->getRealPath();

        $script = [
            "win" => "php %s %s getNonBlockingAndSend %s > NUL",
            "lin" => "bash -c \"exec php %s %s getNonBlockingAndSend %s > /dev/null 2>&1 &\""
        ];

        $scriptOptions = [
            $path,
            base64_encode(Telegram::class),
            base64_encode(serialize($options))
        ];

        exec(vsprintf($script[OS], $scriptOptions));
    }

    /**
     * @param string|null $options
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return bool
     */
    public function getNonBlockingAndSend(string $options = null): bool
    {
        if (is_null($options)) {
            return false;
        }

        $options = unserialize(base64_decode($options));

        if ($this->locked()) {
            $this->send($options);
            $this->unlock();
        }

        return true;
    }

    /**
     * @param string $key
     * @param string $url
     * @param mixed $a
     * @param string $header
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return void
     */
    private function _send(string $key, string $url, mixed $a, string $header): void
    {
        $url = "https://api.telegram.org/{$key}/{$url}";
        $response = false;

        if (self::$verbose) {
            echo var_export([$url, $a], true) . PHP_EOL;
        }

        if (self::$enableLogging) {
            app()->getShared("logger.telegram")->debug(var_export([$url, $a], true));
        }

        if (self::$counter > 0) {
            usleep(500000);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $a);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$header]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!self::$isTest) {
            $result = curl_exec($ch);

            curl_close($ch);

            self::$lastResult = $result;
            self::$lastResult = json_decode(self::$lastResult, true);

            $response = !empty(self::$lastResult["ok"]);
        }

        if (self::$verbose) {
            echo var_export($result, true) . PHP_EOL;
        }

        if (self::$enableLogging) {
            app()->getShared("logger.telegram")->debug(var_export($result, true));
        }

        self::$counter++;
    }

    /**
     * @param string $str
     * @param int $l
     * @param string $e
     * @return array
     */
    private function chunk_split_unicode(string $str, int $l = 255, string $e = "\r\n"): array
    {
        $a = array_chunk(preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $l);

        foreach ($a as $k => $v) {
            $a[$k] = implode("", $v);
        }

        return $a;
    }
}

if (!empty($argv)) {
    require_once __DIR__ . "/../../../vendor/autoload.php";

    $app = require_once __DIR__ . "/../../../bootstrap/app.php";

    $kernel = $app->getShared("kernel.telegram");

    $kernel->run($argv);
}
