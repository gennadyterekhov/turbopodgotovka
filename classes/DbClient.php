<?php

require_once dirname(__FILE__) . '/Config.php';

class DbClient
{
    public $dbName;
    public $pdo;


    public function __construct($config = null)
    {
        $config = ($config) ? $config : new Config(dirname(__FILE__) . '/../config/db.json');

        $this->dbName = $config->get('DB_NAME');

        $configLogin = $config->get('DB_USER');
        $configPassword = $config->get('DB_PASS');
        $configDsn = 'mysql:dbname=' . $this->dbName . ';host=' . $config->get('DB_HOST');
        $this->pdo = new PDO(
            $configDsn,
            $configLogin,
            $configPassword,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    // шаблон. выполняет пришедшую строку как СКЛ запрос
    public function executeQuery($query)
    {
        try {
            $statement = $this->pdo->query($query);
        } catch (Exception $e) {
            echo 'Exception when executing query: ' . $query . ' ' . $e->getMessage() . PHP_EOL;
            return false;
        }
        return $statement;
    }


    // создать таблицу
    public function select($tableName)
    {
        $dbName = $this->dbName;
        $query = <<<EOD
        SELECT * FROM $dbName.$tableName;
EOD;
        return $this->executeQuery($query);
    }



    // взять по id
    public function get($id, $tableName)
    {
        $dbName = $this->dbName;
        $query = <<<EOD
        SELECT * FROM $dbName.$tableName
        WHERE id='$id';
EOD;
        return $this->executeQuery($query);
    }



    // удалить
    public function delete($id, $tableName)
    {
        $dbName = $this->dbName;
        $query = <<<EOD
        DELETE FROM $dbName.$tableName
        WHERE id='$id';
EOD;
        return $this->executeQuery($query);
    }


    // удалить все данные в таблице
    public function deleteAll($tableName)
    {
        $dbName = $this->dbName;
        $query = <<<EOD
        DELETE FROM $dbName.$tableName;
EOD;
        return $this->executeQuery($query);
    }
}

