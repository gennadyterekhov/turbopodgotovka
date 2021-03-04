<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers');
header('Content-type: application/json');

require_once dirname(__FILE__) . '/../classes/Config.php';
require_once dirname(__FILE__) . '/../classes/DbClient.php';
require_once dirname(__FILE__) . '/../classes/Router.php';
require_once dirname(__FILE__) . '/../classes/Entities/User.php';
require_once dirname(__FILE__) . '/../classes/Entities/Log.php';
require_once dirname(__FILE__) . '/../classes/Entities/Event.php';


//настройки php


ini_set('error_reporting', E_ALL);
ini_set('error_log', __DIR__ . '/_logs/phpErrors__' . date("d-m-y") . '.log');
ini_set('log_errors', 1);


function getDbOrDie()
{
    $dbConn = [];
    try {
        $dbConn = new DbClient();
    } catch (Exception $excep) {
        $response = [
            'code' => 500,
            'message' => 'не удалось подключиться к базе ' . 'exception ' . $excep->getMessage()
        ];
        $formattedResponse = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo $formattedResponse;
        die();
    }
    return $dbConn;
}


$router = new Router();

$fullRequest = [
    'request' => $_REQUEST,
    'server' => $_SERVER,
    'get' => $_GET,
    'post' => $_POST,
    'php_input' => file_get_contents('php://input')
];
$response = $router->route($fullRequest);

$formattedResponse = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo $formattedResponse;
