<?php

declare(strict_types=1);

if (!defined("TIMEOUT")) {
    define("TIMEOUT", 30);
}

if (!defined("BASE_URL")) {
    define("BASE_URL", "//" . $_SERVER["SERVER_NAME"]);
}

if (!defined("PURPOSE")) {
    define("PURPOSE", (array_key_exists("HTTP_PURPOSE", $_SERVER) ? $_SERVER["HTTP_PURPOSE"] : null));
}

if (!defined("REQUEST_URI")) {
    define("REQUEST_URI", $_SERVER["REQUEST_URI"]);
}

if (!defined("DEFAULT_CONTROLLER")) {
    define("DEFAULT_CONTROLLER", "index");
}

if (!defined("DEFAULT_ACTION")) {
    define("DEFAULT_ACTION", "index");
}

ini_set("max_execution_time", TIMEOUT);

set_time_limit(TIMEOUT);

if (!empty($_SERVER["HTTP_CONTENT_TYPE"]) && str_starts_with($_SERVER["HTTP_CONTENT_TYPE"], "application")) {
    $content_type = strtolower($_SERVER["HTTP_CONTENT_TYPE"]);
} elseif (!empty($_SERVER["HTTP_ACCEPT"]) && str_starts_with($_SERVER["HTTP_ACCEPT"], "application")) {
    $content_type = strtolower($_SERVER["HTTP_ACCEPT"]);
} else {
    $content_type = "application/json";
}

if (!defined("CONTENT_TYPE")) {
    define("CONTENT_TYPE", $content_type);
}

unset($content_type);

$is_ajax = (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest");

if (!defined("IS_AJAX")) {
    define("IS_AJAX", $is_ajax);
}

unset($is_ajax);

$request_method = array_key_exists("HTTP_ACTUAL_REQUEST_TYPE", $_SERVER) ? $_SERVER["HTTP_ACTUAL_REQUEST_TYPE"] : $_SERVER["REQUEST_METHOD"];

if (
    !array_key_exists($request_method, [
        "GET" => null,
        "POST" => null,
        "HEAD" => null,
        "OPTIONS" => null
    ])
) {
    $request_method = "GET";
}

if (!defined("REQ_TYPE")) {
    define("REQ_TYPE", $request_method);
}

unset($request_method);

$auth = null;

if (array_key_exists("HTTP_AUTHORIZATION", $_SERVER)) {
    $auth = trim(str_replace("Bearer", "", $_SERVER["HTTP_AUTHORIZATION"]));
}

if (!defined("AUTH_TOKEN")) {
    define("AUTH_TOKEN", $auth);
}

unset($auth);

$is_compressed_request = 0;

if (array_key_exists("HTTP_CMPRSDREQ", $_SERVER)) {
    $is_compressed_request = trim($_SERVER["HTTP_CMPRSDREQ"]);
}

if (!defined("CMPRSDREQ")) {
    define("CMPRSDREQ", $is_compressed_request);
}

unset($is_compressed_request);

$is_compressed_response = 0;

if (array_key_exists("HTTP_CMPRSDRES", $_SERVER)) {
    $is_compressed_response = trim($_SERVER["HTTP_CMPRSDRES"]);
}

if (!defined("CMPRSDRES")) {
    define("CMPRSDRES", $is_compressed_response);
}

unset($is_compressed_response);

if (REQ_TYPE === "POST") {
    $input = file_get_contents("php://input");

    switch (CMPRSDREQ) {
        case "bz2":
            $input = bzdecompress($input);
            break;
        case "gzip":
            $input = gzuncompress($input);
            break;
        case "zlib":
            $input = gzdecode($input);
            break;
    }

    if (!empty($input)) {
        $_POST = match (CONTENT_TYPE) {
            "application/x-msgpack" => msgpack_unpack($input),
            default => json_decode($input, true)
        };
    }

    if (!defined("RAW_INPUT")) {
        define("RAW_INPUT", $input);
    }

    unset($input);
}

$query = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY);

if (!defined("REQUEST_QUERY")) {
    define("REQUEST_QUERY", $query);
}

unset($query);

$preparse_url = null;

if (array_key_exists("REQUEST_URI", $_SERVER)) {
    $preparse_url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
}

$params = explode("/", $preparse_url);

unset($preparse_url);

$params = array_filter($params, fn ($v) => $v !== "");
$count_params = count($params);

if ($count_params === 0) {
    $controller = DEFAULT_CONTROLLER;
    $action = DEFAULT_ACTION;
} elseif ($count_params === 1) {
    $controller = array_shift($params);
    $action = DEFAULT_ACTION;
} else {
    $controller = array_shift($params);
    $action = array_shift($params);
}

$controller = kebabToPascalCase($controller);
$controller = "Application\\Controllers\\" . (REQ_TYPE === "HEAD" ? "GET" : REQ_TYPE) . "\\" . $controller . "Controller";

unset($controller_array);

if (!defined("CONTROLLER")) {
    define("CONTROLLER", $controller);
}

$original_action = $action;
$action = kebabToCamelCase($action);
$action_name = $action . "Action";

unset($action_array);

if (class_exists($controller)) {
    $controller_object = new $controller();

    unset($controller);
} else {
    throw new \Exception("no controller {$controller}", 404);
}

if (!method_exists($controller_object, $action_name)) {
    array_unshift($params, $original_action);

    $action = "index";
    $action_name = "indexAction";

    unset($original_action);
}

if (!defined("ACTION")) {
    define("ACTION", $action_name);
}

if (!defined("PARAMS")) {
    define("PARAMS", $params);
}

if (method_exists($controller_object, $action_name)) {
    call_user_func_array([$controller_object, $action_name], $params);

    unset($action_name, $action, $params);
} else {
    throw new \Exception("no action {$action_name}", 404);
}

$http_response = $controller_object->getHttpResponse();

$http_response->flush();
