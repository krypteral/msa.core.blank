<?php

declare(strict_types=1);

namespace Cron\Tasks;

final class IndexTask extends Task
{
    /**
     * @return void
     */
    public function indexAction(): void
    {
        echo "indexAction";
    }
}
