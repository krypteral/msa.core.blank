<?php

declare(strict_types=1);

namespace Application\Components\Response;

class JsonResponse extends HttpResponse
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

    /**
     * @var string
     */
    protected string $content_type = "Content-Type: application/json";

    public function flush(): void
    {
        if (!isset($this->body)) {
            $this->body = [
                "success" => $this->success,
                "message" => $this->message
            ];
        }

        $this->body = json_encode($this->body, JSON_UNESCAPED_UNICODE);

        parent::flush();
    }
}
