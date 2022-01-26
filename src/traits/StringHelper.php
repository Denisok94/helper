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
     * человекопонятный URL
     * @param string $source
     * @return string
     */
    public static function ru2Slug($source)
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
    public static function slashes($value)
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
     * - ||spoiler||
     * - ​[spoiler=спойлер]спойлер[/spoiler]
     * - [quote][/quote]
     * - [quote=][/quote]
     * - [url=][/url]
     * - [url][/url]
     * - [img][/img]
     * - [img=]
     * - [size=2][/size] в %
     * - [color=][/color]
     * - [list][/list] - ul
     * - [listn][/listn] - ol
     * - [\*][\*] - li
     * 
     * https://myrusakov.ru/php-parsing-bb.html
     */
    public static function replaceBBCode($text_post)
    {
        $str_search = array(
            "#\\\n#is",  // 1
            "#\[b\](.+?)\[\/b\]#is", // 2
            "#\*\*(.+?)\*\*#is", // 2.2
            "#\[i\](.+?)\[\/i\]#is", // 3
            "#\[u\](.+?)\[\/u\]#is", // 4
            "#\_\_(.+?)\_\_#is", // 4.2
            "#\[s\](.+?)\[\/s\]#is", // 5
            "#\~\~(.+?)\~\~#is", // 5.2
            "#\[code\](.+?)\[\/code\]#is", // 6
            "#\[code=(.+?)\](.+?)\[\/code\]#is", // 6.2
            "#\`\`\`(.+?)\`\`\`#is", // 6.3
            "#\|\|(.+?)\|\|#is", // 6.4
            "#\[spoiler=(.+?)\](.+?)\[\/spoiler\]#is", // 6.5
            "#\[quote\](.+?)\[\/quote\]#is", // 7
            "#\[quote=(.+?)\](.+?)\[\/quote\]#is", // 8
            "#\[url=(.+?)\](.+?)\[\/url\]#is", // 9
            "#\[url\](.+?)\[\/url\]#is", // 10
            "#\[img\](.+?)\[\/img\]#is", // 11
            "#\[img=(.+?)\]#is", // 12
            "#\[size=(.+?)\](.+?)\[\/size\]#is", // 13
            "#\[color=(.+?)\](.+?)\[\/color\]#is", // 14
            "#\[list\](.+?)\[\/list\]#is", // 15
            "#\[listn\](.+?)\[\/listn\]#is", // 16
            "#\[\*\](.+?)\[\/\*\]#" // 17
        );
        $str_replace = array(
            "<br />",  // 1
            "<b>\\1</b>", // 2
            "<b>\\1</b>", // 2.2
            "<i>\\1</i>", // 3
            "<span style='text-decoration:underline'>\\1</span>", // 4
            "<span style='text-decoration:underline'>\\1</span>", // 4.2
            "<span style='text-decoration:line-through'>\\1</span>", // 5
            "<span style='text-decoration:line-through'>\\1</span>", // 5.2
            "<code class='code'>\\1</code>", // 6
            "<code class='code \\1'>\\2</code>", // 6.2
            "<code class='code'>\\1</code>", // 6.3
            // "<span class='spoiler'>\\1</span>", // 6.4
            "<details class=\"spoiler\"><summary class=\"spoiler-title\"><span>спойлер</span></summary><p class=\"spoiler-text\">\\1</p></details>", // 6.4
            "<details class=\"spoiler\"><summary class=\"spoiler-title\"><span>\\1</span></summary><p class=\"spoiler-text\">\\2</p></details>", // 6.5
            // '<input type="checkbox" id="1" class="del" /><label for="1" class="del" align="left">спойлер</label><div>[\\1]</div>', // 6.4
            // '<input type="checkbox" id="1" class="del" /><label for="1" class="del" align="left">\\1</label><div>[\\2]</div>', // 6.5
            "<blockquote class='quote'>\\1</blockquote>", // 7
            "<blockquote class='quote'><p>\\2</p><small>\\1</small></blockquote>", // 8
            "<a href='\\1' target=\"_blank\" >\\2</a>", // 9
            "<a href='\\1' target=\"_blank\" >\\1</a>", // 10
            "<img src='\\1' alt='\\1' class='img'/>", // 11
            "<img src='\\1' alt='\\1' class='img'/>", // 12
            "<span style='font-size:\\1%'>\\2</span>", // 13
            "<span style='color:\\1'>\\2</span>", // 14
            "<ul class='ul'>\\1</ul>", // 15
            "<ol class='ol'>\\1</ol>", // 16
            "<li class='li'>\\1</li>" // 17
        );
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
    public static function strchop($data, $word, $interval, $callback, $ci = true)
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
