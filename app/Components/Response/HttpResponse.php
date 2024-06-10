<?php

declare(strict_types=1);

namespace Application\Components\Response;

abstract class HttpResponse
{
    /**
     * @var int
     */
    protected int $code;

    /**
     * @var string
     */
    protected string $content_type = "Content-Type: text/html; charset=utf-8";

    /**
     * @var mixed
     */
    protected mixed $body;

    /**
     * @var string|null
     */
    protected ?string $COMPRESSED;

    public function __construct($code = 200, $body = null, $COMPRESSED = null)
    {
        $this->code = $code;
        $this->COMPRESSED = $COMPRESSED;

        if (!is_null($body)) {
            $this->setBody($body);
        }
    }

    /**
     * @return mixed
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return void
     */
    public function setBody(mixed $body): void
    {
        $this->body = $body;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->response_header();

        $fp = fopen("php://output", "w");

        if (is_null($this->body)) {
            $this->body = "Unknown error";
        }

        switch ($this->COMPRESSED) {
            case "bz2":
                $this->body = bzcompress($this->body, 9);
                break;
            case "gzip":
                $this->body = gzcompress($this->body, 9);
                break;
            case "zlib":
                $this->body = gzencode($this->body, 9);
                break;
        }

        fwrite($fp, $this->body);
        fclose($fp);
    }

    /**
     * @return void
     */
    protected function response_header(): void
    {
        http_response_code($this->code);
        header($this->content_type);
    }
}
