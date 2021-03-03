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
        $requestUrl = $request['server']['REMOTE_ADDR'];
        $requestUri = $request['server']['REQUEST_URI'];
        $requestScriptName = $request['server']['SCRIPT_NAME'];
        $requestQueryString = $request['server']['QUERY_STRING'];
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

        // REQUEST_URI


        // if (array_key_exists())
        // $this->router



        // var_dump($request);

        // var_dump($requestQueryString);


        // $queryStringData = urldecode($requestQueryString);

        // var_dump($queryStringData);



        $controllerClassName = ucfirst($entity) . 'Controller';
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

            // try {
            //     $response = call_user_func([$controllerClassName, $action]);
            // } catch (Exception $excep) {
            //     $response = 'unknown entity or action';
            // }


        }
        if ($requestMethod === 'POST') {

            // try {
            //     $response = call_user_func([$controllerClassName, $action], $request['post']);
            // } catch (Exception $excep) {
            //     $response = 'unknown entity or action';
            // }


            $allowedActions = [
                'create',
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
                // $data = json_decode($request['php_input'], true);
                // var_dump($data);
                // $debugData = json_encode($data, JSON_PRETTY_PRINT);
                // echo $debugData;
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
        // die();
        // $result = [];
        // $dbConn = getDbOrDie();
        // // $dbApi = new DbApi($dbConn);
        // $user = new User($dbConn);
        // $log = new Log($dbConn);
        // $event = new Event($dbConn);
    
    













        // switch ($request['method']) {
    
        //     case 'create':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = 'create таблицу ' . $request['table'];
    
        //             $tableName = $request['table'];
        //             if ($tableName == 'user') {
        //                 $result = $user->createTableIfNotExists();
        //             }
        //             if ($tableName == 'event') {
        //                 $result = $event->createTableIfNotExists();
        //             }
        //             if ($tableName == 'log') {
        //                 $result = $log->createTableIfNotExists();
        //             }
        //             if ($tableName == 'all') {
        //                 $result[] = $user->createTableIfNotExists();
        //                 $result[] = $event->createTableIfNotExists();
        //                 $result[] = $log->createTableIfNotExists();
        //             }
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
    
        //     case 'create_from_json':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = 'update_from_json таблицу ' . $request['table'];
    
        //             $tableName = $request['table'];
        //             if ($tableName == 'user') {
    
        //                 $filename = __DIR__ . '/../config/user.json';
    
        //                 $dataJsonStr = file_get_contents($filename);
        //                 $data = json_decode($dataJsonStr, true);
                
                
        //                 foreach ($data as $userData) {
        //                     // $this->dbConn()
        //                     if (array_key_exists('first_name', $userData) && array_key_exists('last_name', $userData)) {
        //                         $result['result'] = $user->create($userData);
        //                     } else {
        //                         $result['comments'][] = ' не найдены first_name или last_name';
        //                     }
        //                 }
    
    
        //                 // $result = $user->createFromJson();
        //             }
    
    
    
        //             if ($tableName == 'event') {
    
    
        //                 $filename = __DIR__ . '/../config/event.json';
    
        //                 $dataJsonStr = file_get_contents($filename);
        //                 $data = json_decode($dataJsonStr, true);
                
                
        //                 foreach ($data as $eventData) {
        //                     // $this->dbConn()
        //                     if (array_key_exists('name', $eventData)) {
        //                         $result['result'] = $event->create($eventData);
        //                     } else {
        //                         $result['comments'][] = ' не найден name';
        //                     }
        //                 }
    
    
        //                 // $result = $event->createFromJson();
        //             }
    
    
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
    
    
    
        //     case 'select':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = 'read таблицу ' . $request['table'];
    
        //             $tableName = $request['table'];
        //             if ($tableName == 'user') {
        //                 $result = $user->select()->fetchAll(PDO::FETCH_ASSOC);
        //             }
        //             if ($tableName == 'event') {
        //                 $result = $event->select()->fetchAll(PDO::FETCH_ASSOC);
        //             }
        //             if ($tableName == 'log') {
        //                 $result = $log->select()->fetchAll(PDO::FETCH_ASSOC);
        //             }
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
    
        //     case 'get':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = 'read таблицу ' . $request['table'];
    
        //             $tableName = $request['table'];
        //             if ($tableName == 'user') {
    
        //                 if (array_key_exists('id', $request)) {
        //                     $id = $request['id'];
        //                     $result['comments'][] = 'get таблицу ' . $request['table'];
            
        //                     $result = $user->get($id)->fetchAll(PDO::FETCH_ASSOC);
            
        //                 } else {
        //                     $result['comments'][] = 'не найден параметр id';
        //                 }
    
        //             }
        //             if ($tableName == 'event') {
        //                 if (array_key_exists('id', $request)) {
        //                     $id = $request['id'];
        //                     $result['comments'][] = 'get таблицу ' . $request['table'];
            
        //                     $result = $event->get($id)->fetchAll(PDO::FETCH_ASSOC);
            
        //                 } else {
        //                     $result['comments'][] = 'не найден параметр id';
        //                 }
        //             }
        //             if ($tableName == 'log') {
        //                 if (array_key_exists('id', $request)) {
        //                     $id = $request['id'];
        //                     $result['comments'][] = 'get таблицу ' . $request['table'];
            
        //                     $result = $log->get($id)->fetchAll(PDO::FETCH_ASSOC);
            
        //                 } else {
        //                     $result['comments'][] = 'не найден параметр id';
        //                 }
        //             }
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
    
    
        //     case 'update':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = 'update таблицу ' . $request['table'];
    
        //             $tableName = $request['table'];
        //             if ($tableName == 'user') {
        //                 $result = $user->createTableIfNotExists();
        //             }
        //             if ($tableName == 'event') {
        //                 $result = $event->createTableIfNotExists();
        //             }
        //             if ($tableName == 'log') {
        //                 $result = $log->createTableIfNotExists();
        //             }
        //             if ($tableName == 'all') {
        //                 $result[] = $user->createTableIfNotExists();
        //                 $result[] = $event->createTableIfNotExists();
        //                 $result[] = $log->createTableIfNotExists();
        //             }
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
    
    
    
        //     case 'delete':
        //         if (array_key_exists('table', $request)) {
        //             $result['comments'][] = ('delete данные из таблицы ' . $request['table']);
        //             $result = $dbConn->deleteEverythingFromTable($request['table'])->execute();
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
        //     case 'drop':
        //         if (array_key_exists('table', $request)) {
        //             if ($request['table'] == 'all') {
        //                 $dbConn->dropTable('log')->execute();
        //                 $dbConn->dropTable('user')->execute();
        //                 $dbConn->dropTable('event')->execute();
        //             } else {
        //                 $result = $dbConn->dropTable($request['table'])->execute();
        //             }
        //         } else {
        //             $result['comments'][] = 'не найден параметр table';
        //         }
        //         break;
    
        //     default:
        //         $result = 'unknown method';
        // }
    
        // return $result;
        
    }



}
