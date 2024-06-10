<?php

declare(strict_types=1);

namespace Application\Controllers;

use Application\Components\Response\ErrorHttpResponse;
use Application\Components\Response\HttpResponse;
use Application\Components\Response\HttpResponseFactory;
use Application\Components\Response\JsonResponse;
use Application\Components\Response\MessagePackResponse;
use Application\Components\Response\OkHttpResponse;

/**
 * @property MessagePackResponse|JsonResponse $httpResponseClassName
 */
abstract class Controller
{
    /**
     * @var HttpResponse
     */
    protected HttpResponse $http_response;

    public function __destruct()
    {
        if (!isset($this->http_response)) {
            $this->setHttpResponse(new ErrorHttpResponse());
        }
    }

    public function __get($name)
    {
        if ($name === "httpResponseClassName") {
            $this->$name = HttpResponseFactory::get(CONTENT_TYPE);

            return $this->$name;
        }
    }

    /**
     * @return HttpResponse
     */
    public function getHttpResponse(): HttpResponse
    {
        return $this->http_response ?? $this->http_response = new OkHttpResponse(200, "");
    }

    /**
     * @param HttpResponse $http_response
     * @return bool
     */
    public function setHttpResponse(HttpResponse $http_response): bool
    {
        $this->http_response = $http_response;

        return true;
    }
}
