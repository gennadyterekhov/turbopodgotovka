<?php

require_once dirname(__FILE__) . '/../DbClient.php';
require_once dirname(__FILE__) . '/Entity.php';

class Log extends Entity
{
    // public $dbConn;
    // public $tableName;

    public function __construct($dbConn)
    {
        parent::__construct($dbConn, ['user_id', 'event_id', 'event_time']);
    }


    // создать таблицу
    public function createTableIfNotExists()
    {
        $dbName = $this->dbConn->dbName;
        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS
        $dbName.log
        (
            id INT(10) NOT NULL auto_increment,
            user_id INT(10) NOT NULL,
            event_id INT(10) NOT NULL,
            event_time TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES $dbName.user(id),
            FOREIGN KEY (event_id) REFERENCES $dbName.event(id),
            PRIMARY KEY (id)
        )
        DEFAULT CHARSET=utf8;
EOD;
        return $this->dbConn->executeQuery($query);
    }


    // создать строку в таблице
    public function create($userId, $eventId, $eventTime)
    {


        // $fields = $this->fields;
        // $params = [];
        // foreach ($fields as $field) {
        //     if (array_key_exists($field, $request) == false) {
        //         return ['code' => 500, 'message' => "$field not found in request"];
        //     }
        //     // $params[$fieldName] = $fieldValue;
        //     $params[] = $request[$field];
        // }

        // // return call_user_func_array([$this->entity, 'create'], $params)->execute();
        // call_user_func_array([$this->entity, 'create'], $params);
        // return ['code' => 200];











        // $username = $data['username'];
        // $firstName = $data['first_name'];
        // $lastName = $data['last_name'];

        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;

        $query = <<<EOD
        INSERT INTO $dbName.$tableName (
            user_id, event_id, event_time
        ) VALUES (
            '$userId', '$eventId', '$eventTime'
        );
EOD;
        return $this->dbConn->executeQuery($query);
    }



    // обновить
    // public function update($data)
    public function update($id, $userId, $eventId, $eventTime)
    {
        // $id = $data['id'];
        // $userId = $data['user_id'];
        // $eventId = $data['event_id'];
        // $eventTime = $data['event_time'];

        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;
        $query = <<<EOD
        UPDATE $dbName.$tableName
        SET
            user_id='$userId',
            event_id='$eventId',
            event_time='$eventTime'
        WHERE id='$id';
EOD;
        return $this->dbConn->executeQuery($query);
    }

}

