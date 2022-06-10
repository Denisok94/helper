<?php

namespace denisok94\helper\traits;

/**
 * DataHelper trait
 * @author vitaliy-pashkov 
 */
trait DataHelper
{
    /**
     * @param string $toFormat
     * @return string
     */
    public static function currentDate(string $toFormat = 'Y-m-d')
    {
        return (new \DateTime())->format($toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     */
    public static function currentDt(string $toFormat = 'Y-m-d H:i:s')
    {
        return (new \DateTime())->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toMysqlDate(string $date, string $fromFormat = 'd.m.Y', string $toFormat = 'Y-m-d')
    {
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toMysqlDt(string $date, string $fromFormat = 'd.m.Y H:i', string $toFormat = 'Y-m-d H:i:s')
    {
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toRuDate(string $date, string $fromFormat = 'Y-m-d', string $toFormat = 'd.m.Y')
    {
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toRuDt(string $date, string $fromFormat = 'Y-m-d H:i:s', string $toFormat = 'd.m.Y H:i')
    {
        return (\DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     * @deprecated Не актуален, используйте: `currentDate()`
     */
    public static function getTodayDb(string $toFormat = 'Y-m-d')
    {
        return (new \DateTime())->format($toFormat);
    }

    /**
     * @param integer|string $timestamp
     * @param string $toFormat
     * @return string
     * @deprecated Не актуален, используйте: `stampToDtU()`
     */
    public static function toMysqlDtU($timestamp, string $toFormat = 'Y-m-d H:i:s')
    {
        return DataHelper::stampToDtU($timestamp, $toFormat);
    }

    /**
     * @param integer|string $timestamp
     * @param string $toFormat
     * @return string
     */
    public static function stampToDtU($timestamp, string $toFormat = 'Y-m-d H:i:s')
    {
        $dtu = explode(".", $timestamp);
        return (new \DateTime())->setTimestamp($dtu[0])->format($toFormat) . "." . (isset($dtu[1]) ? substr($dtu[1], 0, 6) : "000000");
    }

    /**
     * @param integer $timestamp
     * @param string $toFormat
     * @return string
     */
    public static function stampToDt(int $timestamp, string $toFormat = 'Y-m-d H:i:s')
    {
        return (new \DateTime())->setTimestamp($timestamp)->format($toFormat);
    }
}
