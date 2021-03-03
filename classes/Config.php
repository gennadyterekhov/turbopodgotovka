<?php


class Config {

    private $configFilePath;


    public function __construct($configFilePath='') {
        if (empty($configFilePath)) {
            $configFilePath = dirname(__FILE__) . '/../config/env.json';
        }
        $this->configFilePath = $configFilePath;

        self::createFileIfNotExists($configFilePath);
    }

    // определить тестовая среда или нет
    public static function isTest(){
        return false;
    }
    

    public static function createFileIfNotExists($filePath) {
        try {
            if(!is_file($filePath)){
                file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
            }
        } catch (Exception $exception) {
            echo('исключение при попытке поздания файла конфига');
            echo($exception->getMessage());
        }
    }

      
    // взять переменую из файла конфига
    public static function getConfigVarFromFile($varName, $configFilePath='env.json') {
        if (!$configFilePath) {
            $configJsonStr = file_get_contents(dirname(__FILE__) . '/../config/env.json');
        } else {
            $configJsonStr = file_get_contents($configFilePath);
        }
        $configArr = json_decode($configJsonStr, true);
        return $configArr[$varName];
    }
    

    // взять переменую из файла конфига
    public function get($varName) {
        $varValue = [];
        try {
            $configJsonStr = file_get_contents($this->configFilePath);
            $configArr = json_decode($configJsonStr, true);
            if (array_key_exists($varName, $configArr)) {
                $varValue = $configArr[$varName];
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo('исключение когда беру переменную ' . $varName . ' из конфига');
            echo('вот сам конфиг ' . PHP_EOL . $configJsonStr);
            return false;
        }
        return $varValue;
    }

}
