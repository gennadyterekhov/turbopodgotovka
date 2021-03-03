<?php

require_once dirname(__FILE__) . '/Config.php';
require_once dirname(__FILE__) . '/DbClient.php';
require_once dirname(__FILE__) . '/../classes/Controllers/UserController.php';
require_once dirname(__FILE__) . '/../classes/Controllers/LogController.php';
require_once dirname(__FILE__) . '/../classes/Controllers/EventController.php';

class Router
{
    public $router;


    public function __construct()
    {
        $this->router = [];
    }

    public function addRoute($url, $function)
    {
        $this->router[$url] = $function;
    }


    public function getDbOrDie()
    {
        $dbConn = [];
        try {
            $dbConn = new DbClient();
        } catch (Exception $excep) {
            echo ('не удалось подключиться к базе');
            echo ('exception ' . $excep->getMessage());
            echo ('---------Завершение работы скрипта---------');
            die();
        }
        return $dbConn;
    }


    public function route($request)
    {
        $response = [];
        $requestMethod = $request['server']['REQUEST_METHOD'];

        $entity = $request['get']['entity'];
        $action = $request['get']['action'];


        $dbConn = $this->getDbOrDie();
        $user = new User($dbConn);
        $log = new Log($dbConn);
        $event = new Event($dbConn);

        $userController = new UserController($user);
        $logController = new LogController($log);
        $eventController = new EventController($event);

        if ($requestMethod === 'GET') {
            
            $allowedActions = [
                'get',
                'select'
            ];

            if (in_array($action, $allowedActions)) {
                switch ($entity) {
        
                    case 'user':
                        $response = call_user_func([$userController, $action], $request['get']);
                        break;
            
                    case 'log':
                        $response = call_user_func([$logController, $action], $request['get']);
                        break;
    
                    case 'event':
                        $response = call_user_func([$eventController, $action], $request['get']);
                        break;
    
            
                    default:
                        $response = 'unknown entity';
                }
            } else {
                $response = 'unknown action';
            }

        }
        if ($requestMethod === 'POST') {

            $allowedActions = [
                'create',
                'createFromJson',
                'update',
                'delete',
                'deleteAll'
            ];


            if (in_array($action, $allowedActions)) {
                $data = [
                    'post' => $request['post'],
                    'php_input' => $request['php_input']
                ];
                $data = $data['post'];
                switch ($entity) {
    
                    case 'user':
                        $response = call_user_func([$userController, $action], $data);
                        break;
            
                    case 'log':
                        $response = call_user_func([$logController, $action], $data);
                        break;
    
                    case 'event':
                        $response = call_user_func([$eventController, $action], $data);
                        break;
    
            
                    default:
                        $response = 'unknown entity';
                }
            } else {
                $response = 'unknown action';
            }
        }
        return $response;
    }
}
