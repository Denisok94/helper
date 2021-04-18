<?php

namespace denisok94\helper\traits;

/**
 * OtherHelper trait
 * @author Denisok94
 * @link https://s-denis.ru/git/helper
 * @version 0.2
 */
trait OtherHelper
{

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

}