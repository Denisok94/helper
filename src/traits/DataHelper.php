<?php

namespace denisok94\helper\traits;

use DateTime, DateTimeInterface, DateInterval, DateTimeZone;

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
    public static function currentDate(string $toFormat = 'Y-m-d'): string
    {
        return (new DateTime())->format($toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     */
    public static function currentDt(string $toFormat = 'Y-m-d H:i:s'): string
    {
        return (new DateTime())->format($toFormat);
    }

    /**
     * @param string $dateString https://www.php.net/manual/ru/dateinterval.createfromdatestring.php
     * @param string $toFormat
     * @return string
     * 
     * ```php
     * H::createDate('yesterday'); // yesterday
     * H::createDate('-1 day'); // yesterday
     * H::createDate('1 day'); // tomorrow
     * ```
     */
    public static function createDate(string $dateString = 'yesterday', string $toFormat = 'Y-m-d'): string
    {
        return (new DateTime())->add(DateInterval::createFromDateString($dateString))->format($toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     */
    public static function yesterdayDate(string $toFormat = 'Y-m-d'): string
    {
        return self::createDate('yesterday', $toFormat);
    }

    /**
     * @param DateTime|string $date
     * @param string $modify https://www.php.net/manual/en/datetime.modify
     * @param string $toFormat
     * @return string
     * 
     * ```php
     * H::modifyDate('2006-12-12', '-1 day'); // 2006-12-11
     * H::modifyDate(H::currentDate(), '+1 day'); // tomorrow
     * H::modifyDate(H::currentDt(), '-1 day', 'Y-m-d H:i:s'); // yesterday
     * ```
     */
    public static function modifyDate($date, string $modify = '+1 day', string $toFormat = 'Y-m-d'): string
    {
        $dto = ($date instanceof DateTime) ? $date : (new DateTime((string) $date));
        return $dto->modify($modify)->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toMysqlDate($date, string $fromFormat = 'd.m.Y', string $toFormat = 'Y-m-d'): string
    {
        return self::fromFormat($date, $fromFormat, $toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toMysqlDt(string $date, string $fromFormat = 'd.m.Y H:i', string $toFormat = 'Y-m-d H:i:s'): string
    {
        return self::fromFormat($date, $fromFormat, $toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toRuDate(string $date, string $fromFormat = 'Y-m-d', string $toFormat = 'd.m.Y'): string
    {
        return self::fromFormat($date, $fromFormat, $toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function toRuDt(string $date, string $fromFormat = 'Y-m-d H:i:s', string $toFormat = 'd.m.Y H:i:s'): string
    {
        return self::fromFormat($date, $fromFormat, $toFormat);
    }

    /**
     * @param string $toFormat
     * @return string
     * @deprecated Не актуален, используйте/used: `currentDate()`
     */
    public static function getTodayDb(string $toFormat = 'Y-m-d'): string
    {
        return (new DateTime())->format($toFormat);
    }

    /**
     * @param integer|float|string $timestamp
     * @param string $toFormat
     * @return string
     * @deprecated Не актуален, используйте/used: `stampToDtU()`
     */
    public static function toMysqlDtU($timestamp, string $toFormat = 'Y-m-d H:i:s'): string
    {
        return self::stampToDtU($timestamp, $toFormat);
    }

    /**
     * @param integer|float|string $timestamp
     * @param string $toFormat
     * @return string
     */
    public static function stampToDtU($timestamp, string $toFormat = 'Y-m-d H:i:s'): string
    {
        $dtu = explode(".", $timestamp);
        return (new DateTime())->setTimestamp($dtu[0])->format($toFormat) . "." . (isset($dtu[1]) ? substr($dtu[1], 0, 6) : "000000");
    }

    /**
     * @param integer $timestamp
     * @param string $toFormat
     * @return string
     */
    public static function stampToDt(int $timestamp, string $toFormat = 'Y-m-d H:i:s'): string
    {
        return (new DateTime())->setTimestamp($timestamp)->format($toFormat);
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return int
     * 
     * ```php
     * H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s'); // 1222030800 (This will differ depending on your server time zone...)
     * H::getStamp('22-09-2008 00:00:00', 'd-m-Y H:i:s', 'UTC'); // 1222041600
     * H::getStamp(H::currentDt())
     * ```
     */
    public static function getStamp(string $date, string $fromFormat = 'Y-m-d H:i:s', string $timeZone = null): int
    {
        return (DateTime::createFromFormat($fromFormat, $date, $timeZone ? new DateTimeZone($timeZone) : null))->getTimestamp();
    }

    /**
     * @param string $date
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    public static function fromFormat(string $date, string $fromFormat = 'Y-m-d H:i:s', string $toFormat = 'd.m.Y H:i:s'): string
    {
        return (DateTime::createFromFormat($fromFormat, $date))->format($toFormat);
    }

    /**
     * @param DateTime|string|null $date
     * @return integer
     */
    public static function getWeek($date = null): int
    {
        $dto = $date ? (($date instanceof DateTime) ? $date : (new DateTime((string) $date))) : new DateTime();
        return (int) $dto->format("W");
    }

    /**
     * @param DateTime|integer|null $date
     * @return integer
     */
    public static function getQuarter($date = null): int
    {
        $m = $date ? (($date instanceof DateTime) ? $date->format('m') : (int) $date) : (new DateTime())->format('m');
        return floor(($m - 1) / 3) + 1;
    }

    /**
     * @param DateTime|string|null $date
     * @param string $toFormat
     * @return array [start,end,q]
     * 
     * ```php
     * H::getStartAndEndDateQuarterByDate('2022-12-12'); // ['start' => '2022-10-01','end' => '2022-12-31', 'q' => 4]
     * H::getStartAndEndDateQuarterByDate(new DateTime('2022-12-12'));
     * H::getStartAndEndDateQuarterByDate(); // current
     * ```
     */
    public static function getStartAndEndDateQuarterByDate($date = null, string $toFormat = 'Y-m-d')
    {
        $dto = $date ? (($date instanceof DateTime) ? $date : (new DateTime((string) $date))) : new DateTime();
        $y = $dto->format('Y');
        $m = $dto->format('m');
        switch ($m) {
            case $m >= 1 && $m <= 3:
                $start = new DateTime('01-01-' . $y);
                $q = 1;
                break;
            case $m >= 4 && $m <= 6:
                $start = new DateTime('01-04-' . $y);
                $q = 2;
                break;
            case $m >= 7 && $m <= 9:
                $start = new DateTime('01-07-' . $y);
                $q = 3;
                break;
            case $m >= 10 && $m <= 12:
                $start = new DateTime('01-10-' . $y);
                $q = 4;
                break;
        }
        return array(
            'start' => $start->format($toFormat),
            'end' => $start->modify('+2 month')->modify('Last day of this month')->format($toFormat),
            'q' => $q
        );
    }

    /**
     * @param DateTime|string|null $date
     * @param string $format
     * @return array [first,last]
     *
     * ```php
     * H::getStartAndEndDateByMonth('2022-12-12'); // ['first' => '2022-12-01','last' =>'2022-12-31']
     * H::getStartAndEndDateByMonth(new DateTime('2022-12-12'));
     * H::getStartAndEndDateByMonth(); // current
     * ```
     */
    public static function getStartAndEndDateMonthByDate($date = null, string $format = 'Y-m-d'): array
    {
        $dto = $date ? (($date instanceof DateTime) ? $date : (new DateTime((string) $date))) : new DateTime();
        $y = $dto->format('Y');
        $m = $dto->format('m');
        $dto1 = (new DateTime())->setTimestamp(mktime(0, 0, 0, $m, 1, $y));
        // $dto2 = new DateTime(date('Y-m-t', mktime(0, 0, 0, $month, 1, $year)));
        return [
            'first' => $dto1->format($format),
            'last' => $dto1->modify('Last day of this month')->format($format)
        ];
    }
}
