<?php

require_once dirname(__FILE__) . '/../../../../lib/init.php';
require_once dirname(__FILE__) . '/DbClient.php';


class DbApi
{
    public $dbConn;

    public function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }


    public function getShopsData($longitude, $latitude, $limit)
    {

        HU::log('делаем запрос к бд на поиск всех магазинов');
        $shops = $this->dbConn->selectAllShops()->fetchAll(PDO::FETCH_ASSOC);
        HU::log('результат');
        HU::log($shops);
        $allData = [];
        // по этим магазинам беру вакансии
        foreach ($shops as $shop) {
            HU::log('считаем расстояние до магазина');
            $shop['distance'] = self::distance($shop['latitude'], $shop['longitude'], $latitude, $longitude, 'K');
            HU::log($shop['distance']);
            if ($shop['distance'] <= $limit) {
                HU::log('расстояние до магазина меньше лимита');
                HU::log('делаем запрос к бд на поиск вакансий в магазине');
                $vacancies = $this->dbConn->selectAllVacanciesFromShop($shop['id'])->fetchAll(PDO::FETCH_ASSOC);
                $shop['vacancies'] = $vacancies;
                $allData[] = $shop;
                HU::log('результат');
                HU::log($vacancies);
                HU::log('цельный массив магазина с вакансиями');
                HU::log($shop);
            }
        }

        HU::log('цельный массив всех магазинов с вакансиями вне цикла но который обозначен вне цикла');
        HU::log($allData);

        return $allData;
    }


    public static function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }


    public function updateShops($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            try {
                HU::log('проверим есть ли в базе');

                $shopFromDb = $dbConn->getShopById($shop['marketId'])->fetchAll(PDO::FETCH_ASSOC);

                $formattedResponse = json_encode($shopFromDb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                HU::log('вот результат поиска по базе');
                HU::log($formattedResponse);
                if ($shopFromDb) {
                    HU::log('магазин есть в базе, обновляем');
                    $dbConn->updateShop(
                        $shop['marketId'],
                        $shop['address'],
                        $shop['clientName'],
                        $shop['long'],
                        $shop['lat'],
                        $shop['totalNeed'],
                        $shop['totalRealNeed']
                    );
                } else {
                    HU::log('магазина нет в базе, добавляем');
                    $dbConn->addRow(
                        $shop['marketId'],
                        $shop['address'],
                        $shop['clientName'],
                        $shop['long'],
                        $shop['lat'],
                        $shop['totalNeed'],
                        $shop['totalRealNeed']
                    );
                }
            } catch (Exception $excep) {
                HU::log('exception ' . $excep->getMessage());
            }
        }
    }


    public function addShopsFromHrmData($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            try {
                $dbConn->addRow(
                    $shop['marketId'],
                    $shop['address'],
                    $shop['clientName'],
                    $shop['long'],
                    $shop['lat'],
                    $shop['totalNeed'],
                    $shop['totalRealNeed']
                );

            } catch (Exception $excep) {
                HU::log('exception ' . $excep->getMessage());
            }
        }
    }


    public function updateVacancies($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            foreach ($shop['vacancies'] as $vacancy) {
                try {
                    HU::log('проверим есть ли в базе');

                    $vacancyFromDb = $dbConn->getVacancyById($vacancy['vacancyId'])->fetchAll(PDO::FETCH_ASSOC);

                    $formattedResponse = json_encode($vacancyFromDb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    HU::log('вот результат поиска по базе');
                    HU::log($formattedResponse);

                    if ($vacancyFromDb) {
                        HU::log('вакансия есть в базе, обновляем');
                        $dbConn->updateVacancy(
                            $vacancy['vacancyId'],
                            $vacancy['vacancyName'],
                            $vacancy['need'],
                            $vacancy['realNeed']
                        );
                    } else {
                        HU::log('вакансии нет в базе, добавляем');
                        $dbConn->addVacancy(
                            $vacancy['vacancyId'],
                            $vacancy['vacancyName'],
                            $vacancy['need'],
                            $vacancy['realNeed']
                        );
                    }
                } catch (Exception $excep) {
                    HU::log('exception ' . $excep->getMessage());
                }
            }
        }
    }


    public function addVacanciesFromHrmData($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            foreach ($shop['vacancies'] as $vacancy) {
                try {
                    HU::log('проверим есть ли в базе');

                    $vacancyFromDb = $dbConn->getVacancyById($vacancy['vacancyId'])->fetchAll(PDO::FETCH_ASSOC);

                    $formattedResponse = json_encode($vacancyFromDb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    HU::log('вот результат поиска по базе');
                    HU::log($formattedResponse);

                    if ($vacancyFromDb) {
                        HU::log('вакансия есть в базе, обновляем');
                        $dbConn->updateVacancy(
                            $vacancy['vacancyId'],
                            $vacancy['vacancyName']
                        );
                    } else {
                        HU::log('вакансии нет в базе, добавляем');
                        $dbConn->addVacancy(
                            $vacancy['vacancyId'],
                            $vacancy['vacancyName']
                        );
                    }
                } catch (Exception $excep) {
                    HU::log('exception ' . $excep->getMessage());
                }
            }
        }
    }


    public function updateShopsVacancies($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            try {
                HU::log('удалим существующие и добавим полученные');
                $dbConn->deleteAllVacanciesInShop($shop['marketId']);

                foreach ($shop['vacancies'] as $vacancy) {
                    $dbConn->addRowToManyToMany(
                        $shop['marketId'],
                        $vacancy['vacancyId']
                    );
                }
            } catch (Exception $excep) {
                HU::log('exception ' . $excep->getMessage());
            }
        }
    }


    public function addShopsVacanciesFromHrmData($hrmData)
    {
        $dbConn = $this->dbConn;
        foreach ($hrmData as $shop) {
            foreach ($shop['vacancies'] as $vacancy) {
                try {
                    $dbConn->addRowToManyToMany(
                        $shop['marketId'],
                        $vacancy['vacancyId'],
                        $vacancy['need'],
                        $vacancy['realNeed']
                    );
                } catch (Exception $excep) {
                    HU::log('exception ' . $excep->getMessage());
                }
            }
        }
    }



    public function getShopsVacanciesRelation()
    {
        $dbConn = $this->dbConn;
        $res = $dbConn->selectAllShopsVacanciesPairs()->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function getVacancies()
    {
        $dbConn = $this->dbConn;
        $res = $dbConn->selectAllVacancies()->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }



    public function getFields($table)
    {
        $dbConn = $this->dbConn;
        $res = $dbConn->getFields($table)->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    public function getTables()
    {
        $dbConn = $this->dbConn;
        $res = $dbConn->getTables()->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
}
