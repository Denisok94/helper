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
     * Сгенерировать GUIDv4
     * @return string
     */
    public static function guid()
    {
        if (function_exists('com_create_guid') === true) {
            return com_create_guid();
        }
        return chr(123) . self::uuid() . chr(125);
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
    public static function random(int $length, string $chars = "abcdefghijklmnopqrstuvwxyz")
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
    public static function spell(int $num, array $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        return $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
    }

    /**
     * человекопонятный URL
     * @param string $source
     * @return string
     */
    public static function slug(string $source)
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
    public static function ru2Lat(string $source)
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
    public static function ruToLat(string $source)
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
     * человекопонятный URL
     * @param string $source
     * @return string
     */
    public static function ru2Slug(string $source)
    {
        return StringHelper::slug(StringHelper::ru2Lat($source));
    }

    /**
     * Get the class through basename
     *
     * @param string|object $class
     * @return string
     */
    public static function getClassName($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }

    /**
     * экранирование
     * @param string $value
     * @return string
     */
    public static function slashes(string $value)
    {
        $value = htmlspecialchars($value); // Заменяем символы ‘<’ и ‘>’на ASCII-код
        $value = trim($value); // Удаляем лишние пробелы
        $value = addslashes($value); // Экранируем запрещенные символы
        return $value;
    }

    /**
     * Парсинг BB-кодов
     * 
     * @param string $text
     * @return string
     * 
     * ```php
     * $this->text = H::slashes($this->text);
     * $this->text = H::replaceBBCode($this->text);
     * ```
     * Поддержка:
     * - [hr]
     * - [h1-6]заголовок[/h1-6]
     * - [b]жирный[/b]
     * - \*\*жирный\*\*
     * - [i]курсив[/i]
     * - [u]подчеркнутый[/u]
     * - \_\_Подчеркнутый\_\_
     * - [s]зачеркнутый[/s]
     * - \~\~зачеркнутый\~\~
     * - [code]code[/code]
     * - [code=php]code[/code]
     * - \`\`\`code\`\`\`
     * - ||спойлер||
     * - ​[spoiler=спойлер]спойлер[/spoiler]
     * - [quote][/quote]
     * - [quote=][/quote]
     * - [a href=][/a]
     * - [url=][/url]
     * - [url][/url]
     * - [img][/img]
     * - [img=]
     * - [size=2][/size] в %
     * - [color=][/color]
     * - [list][/list] - ul
     * - [ul][/ul] - ul
     * - [listn][/listn] - ol
     * - [ol][/ol] - ol
     * - [\*][\*] - li
     * - [li][/li] - li
     * 
     * https://myrusakov.ru/php-parsing-bb.html
     * https://www.hdsw.ru/?p=543
     */
    public static function replaceBBCode(string $text_post)
    {
        $bb_code = [
            "#\\\n#is" => "<br />",
            "#\[hr\]#is" => "<hr>",
            "#\[h([1-6]?)\](.+?)\[\/h([1-6]?)\]#is" => "<h\\1>\\2</h\\3>",
            "#\[b\](.+?)\[\/b\]#is" => "<b>\\1</b>",
            "#\*\*(.+?)\*\*#is" => "<b>\\1</b>",
            "#\[i\](.+?)\[\/i\]#is" => "<i>\\1</i>",
            "#\[u\](.+?)\[\/u\]#is" => "<span style='text-decoration:underline'>\\1</span>",
            "#\_\_(.+?)\_\_#is" => "<span style='text-decoration:underline'>\\1</span>",
            "#\[s\](.+?)\[\/s\]#is" => "<span style='text-decoration:line-through'>\\1</span>",
            "#\~\~(.+?)\~\~#is" => "<span style='text-decoration:line-through'>\\1</span>",
            "#\[p\](.+?)\[\/p\]#is" => "<p>\\1</p>",
            "#\[p=center\](.+?)\[\/p\]#is" => "<p style=\"text-align: center;\">\\1</p>",
            "#\[p=right\](.+?)\[\/p\]#is" => "<p style=\"text-align: right;\">\\1</p>",
            "#\[code\](.+?)\[\/code\]#is" => "<code class='code'>\\1</code>",
            "#\[code=(.+?)\](.+?)\[\/code\]#is" => "<code class='code \\1'>\\2</code>",
            "#\`\`\`(.+?)\`\`\`#is" => "<code class='code'>\\1</code>",
            "#\|\|(.+?)\|\|#is" => "<details class=\"spoiler\"><summary class=\"spoiler-title\"><span>спойлер</span></summary><p class=\"spoiler-text\">\\1</p></details>",
            "#\[spoiler=(.+?)\](.+?)\[\/spoiler\]#is" => "<details class=\"spoiler\"><summary class=\"spoiler-title\"><span>\\1</span></summary><p class=\"spoiler-text\">\\2</p></details>",
            "#\[quote\](.+?)\[\/quote\]#is" => "<blockquote class='quote'>\\1</blockquote>",
            "#\[quote=(.+?)\](.+?)\[\/quote\]#is" => "<blockquote class='quote'><p>\\2</p><small>\\1</small></blockquote>",
            "#\[a href=(.+?)\](.+?)\[\/a\]#is" => "<a href='\\1' target=\"_blank\" >\\2</a>",
            "#\[url=(.+?)\](.+?)\[\/url\]#is" => "<a href='\\1' target=\"_blank\" >\\2</a>",
            "#\[url\](.+?)\[\/url\]#is" => "<a href='\\1' target=\"_blank\" >\\1</a>",
            "#\[img\](.+?)\[\/img\]#is" => "<img loading='lazy' src='\\1' alt='\\1'/>",
            "#\[img=(.+?)\]#is" => "<img loading='lazy' src='\\1' alt='\\1'/>",
            "#\[size=(.+?)\](.+?)\[\/size\]#is" => "<span style='font-size:\\1%'>\\2</span>",
            "#\[color=(.+?)\](.+?)\[\/color\]#is" => "<span style='color:\\1'>\\2</span>",
            "#\[list\](.+?)\[\/list\]#is" => "<ul class='ul'>\\1</ul>",
            "#\[ul\](.+?)\[\/ul\]#is" => "<ul class='ul'>\\1</ul>",
            "#\[listn\](.+?)\[\/listn\]#is" => "<ol class='ol'>\\1</ol>",
            "#\[ol\](.+?)\[\/ol\]#is" => "<ol class='ol'>\\1</ol>",
            "#\[\*\](.+?)\[\/\*\]#" => "<li class='li'>\\1</li>",
            "#\[li\](.+?)\[\/li\]#" => "<li class='li'>\\1</li>",
        ];
        foreach ($bb_code as $key => $value) {
            $str_search[] = $key;
            $str_replace[] = $value;
        }
        return preg_replace($str_search, $str_replace, $text_post);
    }

    /**
     * [test/в тесте] Поиск слова в тексте (строке) и вывод части текста вокруг искомого слова
     * 
     * ```php
     * H::strchop($text, $word, 50, function ($word) {
     *  return "<span class=\"word\">" . $word . "</span>";
     * });
     * ```
     * 
     * @param string $data строка в которой ищем
     * @param string $word что ищем
     * @param integer $interval интервал символов до и символов после
     * @param mixed $callback функция обработки, для выделения слова из текста
     * @param bool $ci нечувствителен к регистру, по умолчанию `true`
     * 
     * @return string|false результат, если нет вхождения - `false`
     * @author almix
     * 
     */
    public static function strchop(string $data, string $word, int $interval, $callback, bool $ci = true)
    {
        $position = $ci ? mb_stripos($data, $word) : mb_strpos($data, $word);
        //ничего нет - вернули false
        if (!$position) return false;
        //Определяем стартовую позицию новой строки
        $start_position = $position - $interval;
        //От конца слова определили конечный интервал
        $end_position = $position + mb_strlen($word) + $interval;
        //Если стартовая позиция отрицательная делаем в 0
        if ($start_position < 0) $start_position = 0;
        //определяем длину новой строки 
        $len = $end_position - $start_position;
        $length = (mb_strlen($data) > $len) ? mb_strripos(mb_substr($data, 0, $len), ' ') : $len;
        //вернули результат
        $kusok = '...' . mb_substr($data, $start_position, $length, 'UTF-8') . '...';
        return str_replace($word, $callback($word), $kusok);
    }
}
