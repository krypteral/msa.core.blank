<?php

declare(strict_types=1);

namespace Application\Controllers\GET;

use Application\Controllers\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->setHttpResponse(new $this->httpResponseClassName());
    }
}
