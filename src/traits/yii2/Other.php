<?php

namespace denisok94\helper\traits\yii2;

use Yii;
use denisok94\helper\traits\ArrayHelper;
use denisok94\helper\traits\DataHelper;

/**
 * Other trait
 */
trait Other
{
    /**
     * консольная команда
     * @param string $path commands Controller/action,
     * @param array $params параметры команды,
     * @param bool $sync фоновая задача или нет,
     * @param string $out путь лог файла с результатом команды,
     * @param string $error путь лог файла с ошибками,
     */
    public static function exec($path, $params, $sync = true, $out = '../runtime/logs/consoleOut', $error = '../runtime/logs/consoleError')
    {
        $dir = Yii::$app->getBasePath();
        $dt = DataHelper::currentDate("d.m.Y");
        $syncStr = $sync ? '&' : '';
        $json = addcslashes(ArrayHelper::toJson($params), '"');
        $command = "php $dir/yii $path --json=\"$json\" >> $out.$dt.log 2>> $error.$dt.log $syncStr";
        exec($command);
    }

    /**
     * Создать/Добавить/обновить кэш
     * @param string $name имя кэша,
     * @param array $array данные, которые надо закэшировать,
     * @return bool успех записи
     */
    public static function setCache($name, $array)
    {
        $fileCache = Yii::$app->getBasePath() . "/cache/$name.json";
        if ((file_exists($fileCache))) {
            $oldArray = ArrayHelper::toArray(file_get_contents($fileCache));
            $newArray = array_merge($oldArray, $array);
        } else {
            $newArray = $array;
        }
        $fpc = file_put_contents($fileCache, ArrayHelper::toJson($newArray), LOCK_EX);
        return ($fpc === false) ? false : true;
    }

    /**
     * Получить кэш
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
