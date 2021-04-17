<?php

namespace denisok94\helper\yii2;

use Yii;
use denisok94\helper\ArrayHelper;
use denisok94\helper\DataHelper;
use denisok94\helper\HtmlHelper;
use denisok94\helper\OtherHelper;
use denisok94\helper\StringHelper;

/**
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.1
 */
class H
{
    /**
     * консольная команда
     * @param string $path commands Controller/action,
     * @param array $params параметры команды,
     * @param bool $sync фоновыя задача или нет,
     * @param string $out путь лог файла с результатом команды,
     * @param string $error путь лог файла с ошибками,
     */
    public static function exec($path, $params, $sync = true, $out = '../runtime/consoleOut', $error = '../runtime/consoleError')
    {
        $dir = Yii::$app->getBasePath();
		$dt = DataHelper::currentDate("d.m.Y");
		$syncStr = $sync ? '&' : '';
		$json = json_encode($params);
		$json = addcslashes($json, '"');
		$command = "php $dir/yii $path --json=\"$json\" >> $out.$dt 2>> $error.$dt $syncStr";
		exec($command);
    }
    
    /**
     * Создать кэш
     * @param string $name имя кэша,
     * @param array $array данные, которые надо закэшировать,
     * @return bool успех записи
     */
    public static function setCache($name, $array)
    {
        $fileCache = Yii::$app->getBasePath() . "/cache/$name.json";
        $fpc = file_put_contents($fileCache, ArrayHelper::toJson($array), LOCK_EX);
        return ($fpc === false) ? false : true;
    }

    /**
     * Добавить/обновить кэш
     * @param string $name имя кэша,
     * @param array $array данные
     * @return bool успех записи
     */
    public static function addCache($name, $array)
    {
        $fileCache = Yii::$app->getBasePath() . "/cache/$name.json";
        $oldArray = (file_exists($fileCache)) ? ArrayHelper::toArray(file_get_contents($fileCache)) : [];
        $newArray = array_merge($oldArray, $array);
        $fpc = file_put_contents($fileCache, ArrayHelper::toJson($newArray), LOCK_EX);
        return ($fpc === false) ? false : true;
    }

    /**
     * Получить из кэша
     * @param string $name имя кэша,
     * @return array|bool
     */
    public static function getCache($name)
    {
        $fileCache = Yii::$app->getBasePath() . "/cache/$name.json";
        if (file_exists($fileCache)) {
            return ArrayHelper::toArray(file_get_contents($fileCache));
        } else {
            return false;
        }
    }
}
