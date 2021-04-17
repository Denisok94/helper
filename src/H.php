<?php

namespace denisok94\helper;

/**
 * Helper Class
 * @version 0.1
 */
class H
{
    /**
     * Сгенерировать uuid v4
     */
    public static function uuid()
    {
        // return uniqid('', true);
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
     * Найти в массиве
     */
    public static function get($array, $path, $nullValue = null)
    {
        $parts = explode('.', $path);
        $key = $parts[0];
        if ($array == null) {
            return $nullValue;
        }
        if (array_key_exists($key, $array)) {
            if (count($parts) <= 1) {
                return $array[$key];
            } else {
                unset($parts[0]);
                return H::get($array[$key], implode('.', $parts), $nullValue);
            }
        } else {
            return $nullValue;
        }
    }

    /**
     * Заменить в массиве
     */
    public static function set(&$array, $path, &$value)
    {
        $parts = explode('.', $path);
        $key = $parts[0];
        if (array_key_exists($key, $array)) {
            if (count($parts) <= 1) {
                $array[$key] = $value;
            } else {
                unset($parts[0]);
                H::set($array[$key], implode('.', $parts), $value);
            }
        } else {
            $array[$key] = [];
            H::set($array[$key], implode('.', $parts), $value);
        }
    }

    /**
     * Заменить шаблон
     */
    public static function parse($string, $context, $nullValue = '')
    {
        $offset = 0;

        while (strpos($string, '${', $offset) == true) {
            $begin = strpos($string, '${', $offset) + 2;
            $end = strpos($string, '}', $begin);
            if (strlen($string) - 1 > $begin) {
                $fullIndex = substr($string, $begin, $end - $begin);
                $value = H::get($context, $fullIndex, $nullValue);
                $string = str_replace('${' . $fullIndex . '}', $value, $string);
            }
            $offset = $begin + 1;
            if ($offset > strlen($string)) {
                break;
            }
        }
        return $string;
    }

    /**
     * Объединяет элементы массива в строку, обернуть текст в кавычки
     * @param string $glue делитель
     * @param array $array 
     * @param string $wrapper кавычки
     * @return string
     */
    public static function implodeWrap($glue, $array, $wrapper)
    {
		$result = [];
		foreach ($array as $key => $value) {
			if (is_numeric($key) && $value != null) {
				$result[] = $wrapper . $value . $wrapper;
			} else {
				$result[] = "null";
			}
		}
		return implode($glue, $result);
    }

	/**
	 * Объединяет элементы массива в строку, с пред обработкой
     * @param string $glue делитель
     * @param array $array 
     * @param mixed $callback функция обработки
     * @return string
	 */
	public static function implodeWith($glue, $array, $callback)
	{
		$clear = [];
		foreach ($array as $value) {
			$callbackReturn = $callback($value);
			if ($callbackReturn != null) $clear[] = $callbackReturn;
		}
		return implode($glue, $clear);
	}

    /**
     * @param array $array массив,
     * @return string
     */
    public static function toJson($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $json json,
     * @return array
     */
    public static function toArray($json)
    {
        return json_decode($json, true);
    }
    
	/**
	 * @param string $url адрес сайта,
	 * @param array|string $params данные/параметры запроса,
	 * @param string $method тип/метод запроса,
	 * @param int $timeout ожидание ответа,
	 * @param array|string $header шапка запроса,
	 * @param string $auth HTTP авторизация ~('login:password'),
	 * @param string $proxy запрос через прокси ~('1.1.1.1:80').
	 * @return array ['url', 'code','error','response']
	 */
	static public function curl($url, $params = null, $method = 'GET', $timeout = 2, $header = null, $auth = null, $proxy = null)
	{
		$curl_header = ($header != null) ? (is_array($header) ? $header : [$header]) : [
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
			"Accept-Encoding: gzip, deflate, br",
			"Connection: keep-alive",
			"Upgrade-Insecure-Requests: 1",
			"Pragma: no-cache",
			"Cache-Control: no-cache",
		];
		$method = strtoupper($method);
		// ----------------------------------
		if ($method == 'GET' and $params != null) $url = $url . '?' . (is_array($params) ? http_build_query($params) : $params);
		// ----------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		// ----------------------------------
		if ($auth != null) curl_setopt($ch, CURLOPT_USERPWD, $auth);
		// ----------------------------------
		switch ($method) {
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_PUT, true);
				break;
			default:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}
		// ----------------------------------
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // следовать за редиректом
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// ----------------------------------
		// подключение к прокси-серверу
		if ($proxy != null) {
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			curl_setopt($ch, CURLOPT_HEADER, true);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, false);
		}
		// ----------------------------------
		$data = curl_exec($ch);
		$error = curl_error($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		// ----------------------------------
		$response = ['url' => $url, 'code' => $code, 'error' => $error, 'response' => $data];
		if ($code != 200) $response['request'] = $params;
		// ----------------------------------
		return $response;
	}

    /**
     * HTML тэг видео
     * @param string $src адрес файла,
     * @param array|string $attribution Атрибуты,
     * @return string
     * @link http://htmlbook.ru/html/video
     */
    public static function html_video($src, $attribution = null)
    {
        $video = ' ';
        // ----------------------------------
        // базавые параметры
        $default = [
            'width' => '500px',
            'height' => "300px",
            'controls' => "controls",
            'preload' => "metadata",
        ];
        // ----------------------------------
        //  информация о файле
        $fname = basename($src);
        $filetupe = pathinfo($fname);
        $ftupe = strtolower($filetupe['extension']); //расширение файла в нижнем регистре
        // указываем тип файла и кодек, если известен
        $a_type = [
            'mp4' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
            'webm' => 'video/webm; codecs="vp8, vorbis"',
            'ogv' => 'video/ogg; codecs="theora, vorbis"'
        ];
        $type = isset($a_type[$ftupe]) ? 'type="' . $a_type[$ftupe] . '"' : '';

        // если заданы параметры
        if ($attribution) {
            // это массив
            if (is_array($attribution)) {
                // совмещаем с базавыми
                $default = array_merge($default, $attribution);
                // генерем строку
                foreach ($default as $Key => $Value) {
                    $video .= $Key . '="' . $Value . '" ';
                }
            } else {
                // генерем строку
                $video .= "$attribution ";
            }
        } else {
            // генерем строку из базавых параметров
            foreach ($default as $Key => $Value) {
                $video .= $Key . '="' . $Value . '" ';
            }
        }
        // 
        $source = '<source src="' . $src . '"' . $type . ' >';

        return '<video ' . $video . '>' . $source . ' Тег video не поддерживается вашим браузером. <a href="' . $src . '">Скачайте видео</a></video>';
    }

    //------------------------
    // обработка даты

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

    // конец обработки даты
    //------------------------
}
