<?php

namespace denisok94\helper;

/**
 * DataHelper Class
 * @author vitaliy-pashkov 
 * @link https://s-denis.ru/git/helper
 * @version 0.1
 */
class DataHelper
{

    public static function currentDate($toFormat = 'Y-m-d')
    {
        return (new \DateTime())->format($toFormat);
    }

    public static function currentDt($toFormat = 'Y-m-d H:i:s')
    {
        return (new \DateTime())->format($toFormat);
    }

    public static function toMysqlDate($date, $fromFormat = 'd.m.Y', $toFormat = 'Y-m-d')
    {
        if ($date == null) {
            return null;
        }
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    public static function toRuDate($date, $fromFormat = 'Y-m-d', $toFormat = 'd.m.Y')
    {
        if ($date == null) {
            return '';
        }
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    public static function toMysqlDt($date, $fromFormat = 'd.m.Y H:i', $toFormat = 'Y-m-d H:i:s')
    {
        if ($date == null) {
            return null;
        }
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    public static function toRuDt($date, $fromFormat = 'Y-m-d H:i:s', $toFormat = 'd.m.Y H:i')
    {
        if ($date == null) {
            return null;
        }
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    public static function getTodayDb($toFormat = 'Y-m-d')
    {
        return (new \DateTime())->format($toFormat);
    }

    /**
     * 
     */
    public static function toMysqlDtU($timestamp, $toFormat = 'Y-m-d H:i:s')
    {
        $dtu = explode(".", $timestamp);
        $dt = new \DateTime();
        $dt->setTimestamp($dtu[0]);
        $dt->format($toFormat);
        return $dt->format($toFormat) . "." . (isset($dtu[1]) ? substr($dtu[1], 0, 6) : "000000");
    }
}
