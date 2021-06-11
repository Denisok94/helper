<?php

namespace denisok94\helper\traits;

/**
 * DataHelper trait
 * @author vitaliy-pashkov 
 */
trait DataHelper
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

    public static function toMysqlDt($date, $fromFormat = 'd.m.Y H:i', $toFormat = 'Y-m-d H:i:s')
    {
        if ($date == null) {
            return null;
        }
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    public static function toRuDate($date, $fromFormat = 'Y-m-d', $toFormat = 'd.m.Y')
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

    /**
     * @deprecated Не актуален, используйте: `currentDate()`
     */
    public static function getTodayDb($toFormat = 'Y-m-d')
    {
        return (new \DateTime())->format($toFormat);
    }

    /**
     * @deprecated Не актуален, используйте: `stampToDtU()`
     */
    public static function toMysqlDtU($timestamp, $toFormat = 'Y-m-d H:i:s')
    {
        return DataHelper::stampToDtU($timestamp, $toFormat);
    }

    /**
     * 
     */
    public static function stampToDtU($timestamp, $toFormat = 'Y-m-d H:i:s')
    {
        $dtu = explode(".", $timestamp);
        return (new \DateTime())->setTimestamp($dtu[0])->format($toFormat) . "." . (isset($dtu[1]) ? substr($dtu[1], 0, 6) : "000000");
    }

    /**
     * 
     */
    public static function stampToDt($timestamp, $toFormat = 'Y-m-d H:i:s')
    {
        return (new \DateTime())->setTimestamp($timestamp)->format($toFormat);
    }
}
