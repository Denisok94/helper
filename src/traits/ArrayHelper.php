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
     * Взять в массиве элемент по ключу
     * @param array $array
     * @param string $path
     * @param bool $nullValue
     * @return array|string|bool
     */
    public static function get(array $array, string $path, $nullValue = null)
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
     * Добавить/заменить элемент в массиве
     * @param array $array
     * @param string $path
     * @param mixed $value
     */
    public static function set(array &$array, string $path, $value)
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
            ArrayHelper::set($array, implode('.', $parts), $value);
        }
    }

    /**
     * Заменить шаблон
     * @param string $string
     * @param array $context
     * @param string $nullValue
     * 
     * @return string
     */
    public static function parse(string $string, array $context, $nullValue = '')
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
     * 
     * @example Пример:
     * ```php
     * $items = H::implodeWrap(',', $ids, "'");  // '1','2','3'
     * ```
     * 
     */
    public static function implodeWrap(string $glue, array $array, string $wrapper)
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
     * 
     * @example Пример:
     * ```php
     * $ids = H::implodeWith(", ", $items, function ($item) {
     *  return "{$item['id']}";
     * }); // 1, 2, 3
     * ```
     */
    public static function implodeWith(string $glue, array $array, $callback)
    {
        $clear = [];
        foreach ($array as $value) {
            $callbackReturn = $callback($value);
            if ($callbackReturn != null) $clear[] = $callbackReturn;
        }
        return implode($glue, $clear);
    }

    /**
     *
     * @param string $glue делитель
     * @param array $array 
     * @param string $key
     *
     * @example Пример:
     * ```php
     * $ids = H::implodeByKey(', ', $items, 'id');  // 1, 2, 3
     * ```
     */
    public static function implodeByKey(string $glue, array $array, string $key)
    {
        $clear = [];
        foreach ($array as $value) {
            $clear[] = ArrayHelper::get($value, $key);
        }
        return implode($glue, $clear);
    }

    /**
     *
     * @param string $glue делитель
     * @param array $array 
     * @param string $key
     * @param string $wrapper кавычки
     *
     * @example Пример:
     * ```php
     * $ids = H::implodeByKeyWrap(', ', $items, 'id', "'");  // '1', '2', '3'
     * ```
     */
    public static function implodeByKeyWrap(string $glue, array $array, string $key, string $wrapper)
    {
        $clear = [];
        foreach ($array as $value) {
            $clear[] = $wrapper . ArrayHelper::get($value, $key) . $wrapper;
        }
        return implode($glue, $clear);
    }

    /**
     *
     * @param string $glue делитель
     * @param array $array 
     *
     * 
     * @example Пример:
     * ```php
     * $array = [
     * 	    ['a','b','c'],
     *   	['d','e','t']
     * ];
     * H::implodeMulti(', ', $array); // a, b, c, d, e, t
     * ```
     */
    public static function implodeMulti(string $glue, array $array)
    {
        $_array = [];
        foreach ($array as $val) {
            $_array[] = is_array($val) ? self::implodeMulti($glue, $val) : $val;
        }
        return implode($glue, $_array);
    }

    /**
     * @param mixed $array array or object
     * @return string json
     */
    public static function toJson(mixed $array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $json
     * @return array
     */
    public static function toArray(string $json)
    {
        return json_decode($json, true);
    }

    /**
     * @param array $array
     * @return object
     */
    public static function arrayToObject(array $array)
    {
        $object = new \stdClass();
        foreach ($array as $key => $value) $object->$key = is_array($value) ? self::arrayToObject($value) : $value;
        return $object;
    }

    /**
     * @param array $array
     * @return object
     * json_decode + json_encode
     */
    public static function array2Object(array $array)
    {
        return json_decode(json_encode($array), false);
    }

    /**
     *
     * @param object $object
     * @return array
     */
    public static function objectToArray(object $object)
    {
        $a = array();
        foreach ($object as $k => $v) $a[$k] = (is_array($v) || is_object($v)) ? self::objectToArray($v) : $v;
        return $a;
    }

    /**
     * @param object $object
     * @return array
     * json_decode + json_encode
     */
    public static function object2Array(object $object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * array order by
     * 
     * arrayOrderBy(array, key, sort_type)
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
