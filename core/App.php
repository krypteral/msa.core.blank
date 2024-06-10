<?php

declare(strict_types=1);

namespace Core;

final class App extends Container
{
    public function __construct()
    {
        self::setInstance($this);
    }
}
