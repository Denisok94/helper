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

    /**
     * Получить IP пользователя
     * @return string
     */
    public static function getUserIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //делим на состовляющии
        $ipr = '';
        if ($ip != "::1") {
            $ips = explode(":", $ip); //ip:port
            $ipr = $ips[0];
        } else {
            $ipr = $ip;
        }
        return $ipr;
    }

    /**
     * Эта функция будет проверять, является ли посетитель роботом
     * @param string &$botname если хотите хнать, имя бота
     * @param array $myBots ваш сисок ботов
     * @return bool true|false
     * @return string $botname - вернёт, если true
     */
    public static function isBot(&$botname = '', $myBots = null)
    {
        $bots = [
            // список известных ботов
            'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
            'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
            'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
            'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
            'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google', 'bing.com',
            'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
            'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex', 'dotnetdotcom',
            'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com', 'Nigma.ru',
            // Выявленные самостоятельно
            'vkShare', 'facebookexternalhit', 'Twitterbot', 'Applebot', 'StatOnlineRuBot', 'Yeti', 'Purebot',
            'TrendsmapResolver', 'SemrushBot', 'Nimbostratus-Bot', 'YandexBot', 'mj12bot', 'YandexImages',
            'BackupLand', 'backupland', 'DotBot', 'BuiltWith', 'python-requests', 'NetcraftSurveyAgent',
            'Ezooms', 'AhrefsBot', 'aiohttp', 'CCBot', 'Konturbot', 'statdom', 'PetalBot', 'LetsearchBot',
            'SafeDNSBot', 'oBot', 'LinkpadBot',
            // всякая фигня
            'libwww-perl', 'libwww', 'perl', 'zgrab', 'curl', 'ApiTool', 'masscan', 'Python',
            // Другие
            'bot', 'bots', 'Bot', 'http', '@'
        ];
        if ($myBots != null) $bots = array_merge($myBots, $bots);
        foreach ($bots as $bot)
            if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                $botname = $bot;
                return true;
            }
        return false;
    }
}