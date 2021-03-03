<?php

require_once dirname(__FILE__) . '/../DbClient.php';
require_once dirname(__FILE__) . '/Entity.php';


class User extends Entity
{
    // public $dbConn;
    // public $tableName;


    public function __construct($dbConn)
    {
        parent::__construct($dbConn, ['username', 'first_name', 'last_name']);
    }


    // создать таблицу
    public function createTableIfNotExists()
    {
        $dbName = $this->dbConn->dbName;
        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS
        $dbName.user
        (
            id INT(10) NOT NULL auto_increment,
            username VARCHAR(63) NULL,
            first_name VARCHAR(63) NOT NULL,
            last_name VARCHAR(63) NOT NULL,
            PRIMARY KEY (id)
        )
        DEFAULT CHARSET=utf8;
EOD;
        return $this->dbConn->executeQuery($query);
    }


    // создать строку в таблице
    public function create($username, $firstName, $lastName)
    {
        // $username = $data['username'];
        // $firstName = $data['first_name'];
        // $lastName = $data['last_name'];

        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;

        $query = <<<EOD
        INSERT INTO $dbName.$tableName (
            username, first_name, last_name
        ) VALUES (
            '$username', '$firstName', '$lastName'
        );
EOD;
        return $this->dbConn->executeQuery($query);
    }



    // public function createFromJson()
    // {
    //     $result = [];
    //     $filename = __DIR__ . '/../config/user.json';

    //     $dataJsonStr = file_get_contents($filename);
    //     $data = json_decode($dataJsonStr, true);


    //     foreach ($data as $user) {
    //         // $this->dbConn()
    //         if (array_key_exists('first_name', $user) && array_key_exists('last_name', $user)) {
    //             $result['result'] = $this->create($user);
    //         } else {
    //             $result['comments'][] = ' не найдены first_name или last_name';
    //         }
    //     }
    //     return $result;
    // }






    // обновить
    public function update($data)
    {
        $id = $data['id'];
        $username = $data['username'];
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];

        $dbName = $this->dbConn->dbName;
        $tableName = $this->tableName;
        $query = <<<EOD
        UPDATE $dbName.$tableName
        SET
            username='$username',
            first_name='$firstName',
            last_name='$lastName'
        WHERE id='$id';
EOD;
        return $this->dbConn->executeQuery($query);
    }



}
