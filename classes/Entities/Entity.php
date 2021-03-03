<?php

require_once dirname(__FILE__) . '/../DbClient.php';


class Entity
{
    public $dbConn;
    public $tableName;
    public $fields;

    public function __construct($dbConn, $fields)
    {
        $this->dbConn = $dbConn;
        $this->tableName = lcfirst(static::class);
        $this->fields = $fields;
    }


    // выбрать все данные из таблицы
    public function select()
    {
        return $this->dbConn->select($this->tableName);
    }

    // взять по id
    public function get($id)
    {
        return $this->dbConn->get($id, $this->tableName);
    }


    // удалить строку по id
    public function delete($id)
    {
        return $this->dbConn->delete($id, $this->tableName);
    }


    // удалить все данные в таблице 
    public function deleteAll()
    {
        return $this->dbConn->deleteEverythingFromTable($this->tableName)->execute();
    }

}
