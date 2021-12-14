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

    /**
     * человекопонятный URL
     * @param string $source
     * @return string
     */
    public static function slug($source)
    {
        // очистить от лишних пробелов, преобразовать в нижний регистр.
        $title = mb_strtolower(trim($source), 'UTF-8');
        // заменить пробелы, знаки пунктуации и прочие "не буквы": дефисом
        return preg_replace('/[^a-z0-9]+/i', '-', $title);
    }

    /**
     * Транслитирование, ГОСТ 7.79-2000, схема А
     * @param string $source
     * @return string
     */
    public static function ru2Lat($source)
    {
        if ($source) {
            $rus = [
                'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
                'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
            ];
            $lat = [
                'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Shch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya',
                'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shch', 'y', 'y', 'y', 'e', 'yu', 'ya'
            ];
            return str_replace($rus, $lat, $source);
        }
    }

    /**
     * Транслитирование, ГОСТ 7.79-2000, схема Б
     * @param string $source
     * @return string
     */
    public static function ruToLat($source)
    {
        if ($source) {
            $rus = [
                'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
                'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
            ];
            $lat = [
                'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Shh', '``', 'Y`', '`', 'E`', 'Yu', 'Ya',
                'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shh', '``', 'y`', '`', 'e`', 'yu', 'ya'
            ];
            return str_replace($rus, $lat, $source);
        }
    }

    /**
     * @param string $source
     * @return string
     */
    public static function ru2Slug($source)
    {
        return StringHelper::slug(StringHelper::ru2Lat($source));
    }
}
