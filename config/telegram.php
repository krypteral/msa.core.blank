<?php

declare(strict_types=1);

return [
    "chat_id_info" => getenv("TELEGRAM_CHAT_INFO"),
    "chat_id_exceptions" => getenv("TELEGRAM_CHAT_EXCEPTIONS"),
    "chat_id_failed" => getenv("TELEGRAM_CHAT_FAILED"),
    "key" => getenv("TELEGRAM_BOT")
];
