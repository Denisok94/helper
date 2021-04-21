<?php

namespace denisok94\helper\traits;

/**
 * ArrayHelper trait
 * @author vitaliy-pashkov 
 * @author Denisok94
 */
trait ArrayHelper
{

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
                return ArrayHelper::get($array[$key], implode('.', $parts), $nullValue);
            }
        } else {
            return $nullValue;
        }
    }

    /**
     * Добавить/заменить в массиве
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
                ArrayHelper::set($array[$key], implode('.', $parts), $value);
            }
        } else {
            $array[$key] = [];
            ArrayHelper::set($array[$key], implode('.', $parts), $value);
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
                $value = ArrayHelper::get($context, $fullIndex, $nullValue);
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
     * array order by
     * 
     * ```php
     * $data = [
     *  ['volume' => 67, 'edition' => 2],
     *  ['volume' => 86, 'edition' => 1],
     *  ['volume' => 85, 'edition' => 6],
     *  ['volume' => 98, 'edition' => 2],
     *  ['volume' => 86, 'edition' => 6],
     *  ['volume' => 67, 'edition' => 7]
     * ];
     * // v1
     * $sorted = H::arrayOrderBy($data, 'volume', SORT_DESC);
     * // v2
     * $sorted = H::arrayOrderBy($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
     * ```
     */
    public static function arrayOrderBy()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

}