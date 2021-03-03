<?php

require_once dirname(__FILE__) . '/../DbClient.php';
require_once dirname(__FILE__) . '/Entity.php';


class Event extends Entity
{
    // public $dbConn;
    // public $tableName;

    public function __construct($dbConn)
    {
        parent::__construct($dbConn, ['name']);
    }


    // создать таблицу
    public function createTableIfNotExists()
    {
        $dbName = $this->dbConn->dbName;
        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS
        $dbName.event
        (
            id INT(10) NOT NULL auto_increment,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        )
        DEFAULT CHARSET=utf8;
EOD;
        return $this->dbConn->executeQuery($query);
    }


    // создать строку в таблице
    public function create($name)
    {
        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;

        $query = <<<EOD
        INSERT INTO $dbName.$tableName (
            name
        ) VALUES (
            '$name'
        );
EOD;
        return $this->dbConn->executeQuery($query);
    }


    // public function createFromJson()
    // {
    //     $result = [];
    //     $filename = __DIR__ . '/../config/event.json';

    //     $dataJsonStr = file_get_contents($filename);
    //     $data = json_decode($dataJsonStr, true);


    //     foreach ($data as $event) {
    //         // $this->dbConn()
    //         if (array_key_exists('name', $event)) {
    //             $result['result'] = $this->create($event);
    //         } else {
    //             $result['comments'][] = ' не найден name';
    //         }
    //     }
    //     return $result;
    // }





    // обновить
    public function update($id, $name)
    {
        // $id = $data['id'];
        // $name = $data['name'];

        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;
        $query = <<<EOD
        UPDATE $dbName.$tableName
        SET
            name='$name'
        WHERE id='$id';
EOD;
        return $this->dbConn->executeQuery($query);
    }


}
