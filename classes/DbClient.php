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













    // создать все нужные таблицы
    public function createAllTables()
    {
        $result = [];
        $result['hrm_vacancies'] = $this->createVacanciesTableIfNotExists();
        $result['shops'] = $this->createShopsTableIfNotExists();
        $result['shops_vacancies'] = $this->createManyToManyTableIfNotExists();
        return $result;
    }


    // создать таблицу
    public function createShopsTableIfNotExists()
    {
        $dbName = $this->dbName;
        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS
        $dbName.shops
        (
            id BIGINT unsigned NOT NULL,
            shop_address VARCHAR(255) NOT NULL,
            shop_name VARCHAR(255) NOT NULL,
            longitude DOUBLE(20, 16) NULL,
            latitude DOUBLE(20, 16) NULL,
            need INT(10) NULL,
            real_need INT(10) NULL,
            PRIMARY KEY (id)
        )
        DEFAULT CHARSET=utf8;
EOD;
        return $this->executeQuery($query);
    }


    public function alterShopsTable()
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        ALTER TABLE
        $dbName.shops
        ADD need INT(10) NULL,
        ADD real_need INT(10) NULL;
EOD;
        return $this->executeQuery($query);
    }


    // создать таблицу для многих ко многим:
    public function createManyToManyTableIfNotExists()
    {
        $dbName = $this->dbName;        
        
        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS $dbName.shops_vacancies
        (
            shop_id BIGINT unsigned NOT NULL,
            vacancy_id BIGINT unsigned NOT NULL,
            need INT(10) NULL,
            real_need INT(10) NULL,
            FOREIGN KEY (shop_id) REFERENCES $dbName.shops(id),
            FOREIGN KEY (vacancy_id) REFERENCES $dbName.hrm_vacancies(id),
            CONSTRAINT pk_shops_vacancies PRIMARY KEY (shop_id, vacancy_id)
        )
        DEFAULT CHARSET=utf8;
EOD;
        
        return $this->executeQuery($query);
    }


    // добавить строку
    public function addRow($id, $shopAddress, $shopName, $longitude, $latitude, $need, $realNeed)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        INSERT INTO $dbName.shops (
            id, shop_address, shop_name, longitude, latitude, need, real_need
        ) VALUES (
            '$id', '$shopAddress', '$shopName', $longitude, $latitude, $need, $realNeed
        );
EOD;

        return $this->executeQuery($query);
    }


    // добавить строку
    public function addVacancy($id, $name)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        INSERT INTO $dbName.hrm_vacancies
        (id, name )
        VALUES ('$id', '$name');
EOD;

        return $this->executeQuery($query);
    }


    // добавить строку в отношение
    public function addRowToManyToMany($shopId, $vacancyId, $need, $realNeed)
    {
        $dbName = $this->dbName;
        $query = "INSERT INTO $dbName.shops_vacancies (shop_id, vacancy_id, need, real_need) VALUES ('$shopId', '$vacancyId', '$need', '$realNeed');";
        return $this->executeQuery($query);
    }

    // обновить магазин
    public function updateShop($id, $shopAddress, $shopName, $longitude, $latitude, $need, $realNeed)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        UPDATE $dbName.shops
        SET
        id='$id',
        shop_address='$shopAddress',
        shop_name='$shopName',
        longitude='$longitude',
        latitude='$latitude',
        need=$need,
        real_need=$realNeed
        WHERE id='$id';
EOD;
        return $this->executeQuery($query);
    }


    // обновить вакансию
    // public function updateVacancy($id, $name, $need, $realNeed)
    public function updateVacancy($id, $name)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        UPDATE $dbName.hrm_vacancies
        SET
        id='$id',
        name='$name'
        WHERE id='$id';
EOD;

        return $this->executeQuery($query);
    }


    // взять shop по id
    public function getShopById($id)
    {
        $dbName = $this->dbName;
        return $this->executeQuery("SELECT * FROM $dbName.shops WHERE id='$id';");
    }


    // взять vacancy по id
    public function getVacancyById($id)
    {
        $dbName = $this->dbName;
        return $this->executeQuery("SELECT * FROM $dbName.hrm_vacancies WHERE id='$id';");
    }


    // выбрать все из нужной таблицы
    public function selectAllShops()
    {
        $dbName = $this->dbName;
        $query = "SELECT * FROM $dbName.shops;";
        return $this->executeQuery($query);
    }


    // выбрать все из вакансии в нужном магазине
    public function selectAllVacanciesFromShop($shopId)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        SELECT
        *
        FROM $dbName.hrm_vacancies
        JOIN $dbName.shops_vacancies
        ON hrm_vacancies.id = shops_vacancies.vacancy_id
        WHERE shops_vacancies.shop_id = '$shopId';
EOD;


        return $this->executeQuery($query);
    }

    // выбрать все магазины с вакансией
    public function selectAllShopsWithVacancy($vacancyId)
    {
        $dbName = $this->dbName;
        $query = "SELECT * FROM $dbName.shops_vacancies where vacancy_id='$vacancyId';";
        return $this->executeQuery($query);
    }


    // выбрать все магазины с вакансией
    public function selectAllVacanciesInShop($shopId)
    {
        $dbName = $this->dbName;
        $query = "SELECT * FROM $dbName.shops_vacancies where shop_id='$shopId';";
        return $this->executeQuery($query);
    }


    // выбрать все магазины с вакансией
    public function selectAllShopsVacanciesPairs()
    {
        $dbName = $this->dbName;
        $query = "SELECT * FROM $dbName.shops_vacancies;";
        return $this->executeQuery($query);
    }

    // выбрать все магазины с вакансией
    public function selectAllVacancies()
    {
        $dbName = $this->dbName;
        $query = "SELECT * FROM $dbName.hrm_vacancies;";
        return $this->executeQuery($query);
    }



    // выбрать магазины которые удовлетворяют лимиту расстояния
    public function selectShopsWithinLimit($xEmployee, $yEmployee, $limit)
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        SELECT id,
        shop_address,
        shop_name,
        longitude,
        latitude,
        SQRT(POWER(($xEmployee - longitude), 2) + POWER(($yEmployee - latitude), 2)) * 111 as distance
        FROM $dbName.shops
        where SQRT(POWER(($xEmployee - longitude), 2) + POWER(($yEmployee - latitude), 2)) * 111 <= $limit;
EOD;


        return $this->executeQuery($query);
    }

    // выбрать все магазины с вакансией
    public function deleteAllVacanciesInShop($shopId)
    {
        $dbName = $this->dbName;
        $query = "DELETE FROM $dbName.shops_vacancies where shop_id='$shopId';";
        return $this->executeQuery($query);
    }


    // удалить все данные из таблицы 
    public function deleteEverythingFromTable($table)
    {
        $dbName = $this->dbName;
        $query = "DELETE FROM $dbName.$table;";
        return $this->executeQuery($query);
    }


    // удалить таблицу
    public function dropTable($table)
    {
        $dbName = $this->dbName;
        $query = "DROP TABLE IF EXISTS $dbName.$table;";
        return $this->executeQuery($query);
    }


    public function createVacanciesTableIfNotExists()
    {
        $dbName = $this->dbName;



        $query = <<<EOD
        CREATE TABLE IF NOT EXISTS
        $dbName.hrm_vacancies
        (
            id BIGINT unsigned NOT NULL,
            name VARCHAR(255) NULL,
            PRIMARY KEY (id)
        )
        DEFAULT CHARSET=utf8;
EOD;

        return $this->executeQuery($query);
    }

    public function alterVacanciesTable()
    {
        $dbName = $this->dbName;

        $query = <<<EOD
        ALTER TABLE
        $dbName.hrm_vacancies
        ADD COLUMN need INT(10) NULL,
        ADD COLUMN real_need INT(10) NULL;
EOD;


        return $this->executeQuery($query);
    }


    public function getFields($table)
    {
        
        $dbName = $this->dbName;

        $query = <<<EOD
        SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = N'$table'
EOD;
        return $this->executeQuery($query);
    }

    public function getTables() {

        $dbName = $this->dbName;

        $query = <<<EOD
        SELECT TABLE_NAME 
        FROM $dbName.INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_TYPE = 'BASE TABLE'
EOD;
        return $this->executeQuery($query);
    }


}

