<?php

declare(strict_types=1);

namespace Application\Components\Response;

class MessagePackResponse extends HttpResponse
{
    /**
     * @var bool
     */
    public bool $success = true;

    /**
     * @var string
     */
    public string $message = "OK";

    /**
     * @var int
     */
    public int $affected_rows = 0;

    /**
     * @var int
     */
    public int $execute_time = 0;

    protected string $content_type = 'Content-Type: application/x-msgpack';

    public function flush(): void
    {
        if (!isset($this->body)) {
            $this->body = [
                "success" => $this->success,
                "message" => $this->message
            ];
        }

        $this->body = msgpack_pack($this->body);

        parent::flush();
    }
}
