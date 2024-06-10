<?php

declare(strict_types=1);

$params = array_filter($argv, fn ($k) => $k !== 0, ARRAY_FILTER_USE_KEY);
$count_params = count($params);

if ($count_params === 0 || $count_params === 1) {
    throw new Exception("missing arguments");
} else {
    $task = array_shift($params);
    $task = "Cron\\Tasks\\" . kebabToPascalCase($task) . "Task";
    $action = array_shift($params);
    $action = lcfirst(kebabToCamelCase($action)) . "Action";
}

if (class_exists($task)) {
    $task_object = new $task();
    unset($task);
} else {
    throw new Exception("no task {$task}");
}

if (method_exists($task_object, $action)) {
    call_user_func_array([$task_object, $action], $params);
    unset($action, $params);
} else {
    throw new Exception("no action {$action}");
}
