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
     * createFromDateString
     * https://www.php.net/manual/ru/dateinterval.createfromdatestring.php
     * @param string $dateString
     * @param string $toFormat
     * @return string
     * 
     * ```php
     * H::createDate('yesterday'); // yesterday
     * H::createDate('-1 day'); // yesterday
     * H::createDate('1 day'); // tomorrow
     * 
     * ```
     */
    public static function createDate(string $dateString = 'yesterday', string $toFormat = 'Y-m-d')
    {
        return (new \DateTime())->add(\DateInterval::createFromDateString($dateString))->format($toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     */
    public static function yesterdayDate(string $toFormat = 'Y-m-d')
    {
        return DataHelper::createDate('yesterday', $toFormat);
    }

    /**
     * @param string $date
     * @param string $dateString
     * @param string $toFormat
     * @return string
     * 
     * ```php
     * H::modifyDate('2006-12-12', '-1 day'); // 2006-12-13
     * H::modifyDate(H::currentDate(), '+1 day'); // tomorrow
     * H::modifyDate(H::currentDt(), '-1 day', 'Y-m-d H:i:s'); // yesterday
     * ```
     */
    public static function modifyDate(string $date, string $dateString = '+1 day', string $toFormat = 'Y-m-d')
    {
        return (new \DateTime($date))->modify($dateString)->format($toFormat);
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

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     * 
     * ```php
     * H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s'); // 1222093324 (This will differ depending on your server time zone...)
     * H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s', 'UTC'); // 1222093289
     * ```
     */
    public static function getStamp(string $date, string $fromFormat = 'Y-m-d H:i:s', string $timeZone = null)
    {
        return (\DateTime::createFromFormat($fromFormat, $date, $timeZone ? new \DateTimeZone($timeZone) : null))->getTimestamp();
    }
}
