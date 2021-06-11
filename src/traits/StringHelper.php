<?php

namespace denisok94\helper\traits;

/**
 * StringHelper trait
 */
trait StringHelper
{
    /**
     * Сгенерировать uuid v4
	 * @return string
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)

        );
    }

    /**
     * 
	 * @param int $length
	 * @param string $chars
     * @return string
     * 
	 * @example Пример:
	 * ```php
	 * $random = H::random(12);
	 * $random = H::random(12, "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789");
	 * $random = H::random(12, "0123456789");
	 * ```
     */
    public static function random($length, $chars = "abcdefghijklmnopqrstuvwxyz")
    {
        $text = '';
        for ($i = 0; $i < $length; $i++) {
            $text .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $text;
    }

    /**
     * 
	 * @param int $num count
	 * @param array $titles ['персона', 'персоны', 'персон', ''];
	 * @return string
     */
    public static function spell($num, $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        return $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
    }
}
