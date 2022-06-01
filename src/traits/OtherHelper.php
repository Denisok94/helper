<?php

namespace denisok94\helper\traits;

/**
 * OtherHelper trait
 */
trait OtherHelper
{
    /**
     * @param string $url адрес сайта,
     * @param array|string $params данные/параметры запроса,
     * @param string $method тип/метод запроса,
     * @param int $timeout ожидание ответа,
     * @param array|string $header шапка запроса,
     * @param string $cookie_file файл для кук,
     * @param string $auth HTTP авторизация ~('login:password'),
     * @param string $proxy запрос через прокси ~('1.1.1.1:80').
     * @return array return:
     * - `url`: url запроса.
     * - `code`: код ответа.
     * - `headers`: заголовки ответа.
     * - `error`: ошибки, если есть.
     * - `response`: тело ответа.
     * - `request`: тело запроса, если code не 200.
     * @author Denisok94
     */
    static public function curl($url, $params = null, $method = 'GET', $timeout = 2, $header = null, $cookie_file = null, $auth = null, $proxy = null)
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
        if ($params != null) {
            $params = is_array($params) ? http_build_query($params) : $params;
            if ($method == 'GET') {
                $url = $url . '?' . $params;
            }
        }
        // ----------------------------------
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        // ----------------------------------
        if ($auth != null) curl_setopt($ch, CURLOPT_USERPWD, $auth);
        // ----------------------------------
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
        if ($method != 'GET' && $params) curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // ----------------------------------
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // следовать за редиректом
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // ----------------------------------
        if ($cookie_file != null) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); // файл, откуда читаются куки
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); // файл, куда пишутся куки
        }
        // ----------------------------------
        $headers = [];
        // this function is called by curl for each header received
        curl_setopt(
            $ch,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $name = strtolower(trim($header[0]));
                if (!array_key_exists($name, $headers))
                    $headers[$name] = [trim($header[1])];
                else
                    $headers[$name][] = trim($header[1]);

                return $len;
            }
        );
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
        $response = ['url' => $url, 'code' => $code, 'headers' => $headers, 'error' => $error, 'response' => $data];
        if ($code != 200) $response['request'] = $params;
        // ----------------------------------
        return $response;
    }

    /**
     * параметры запроса
     * @return array request
     * - `method`: основной метод запроса.
     * - `get`: get параметры, если есть или `null`.
     * - `post`: post параметры, если есть или `null`.
     * - `put`: put параметры, если есть.
     * - `delete`: delete параметры, если есть.
     * - `headers`: заголовки запроса.
     * - `raw`: все request параметры.
     * - `php://input`(old): все request параметры, если есть put или delete.
     */
    public static function getRequest()
    {
        $a = [];
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $a["method"] = $method;
        // ----------------------------------
        foreach ($_GET as $key => $val) {
            $a['get'][$key] = $val;
        }
        if (!isset($a['get'])) $a['get'] = null;
        // ----------------------------------
        foreach ($_POST as $key => $val) {
            $a['post'][$key] = $val;
        }
        if (!isset($a['post'])) $a['post'] = null;
        // ----------------------------------
        if ($method == 'put' or $method == 'delete') {
            $a['php://input'] = file_get_contents('php://input');
            $urldecode = urldecode($a['php://input']);
            $urldecode = explode("&", $urldecode);
            foreach ($urldecode as $value) {
                $value = explode("=", $value);
                $a[$method][$value[0]] = $value[1];
            }
        }
        // ----------------------------------
        $a['raw'] = file_get_contents('php://input');
        // ----------------------------------
        foreach (getallheaders() as $name => $value) {
            $a['headers'][$name] = $value;
        }
        // ----------------------------------
        return $a;
    }

    /**
     * Получить IP пользователя
     * @return string the user ip
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
        //делим на составляющие
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
     * Эта функция будет проверять, является ли посетитель роботом.
     * Она не даёт 100% гарантии. 
     * -------
     * 
     * ```php
     * $user = (H::isBot()) ? ('bot') : ('user');
     * 
     * $bname = '';
     * $user = (H::isBot($bname)) ? ($bname) : ('user');
     * ```
     * 
     * @param string &$botname имя бота, если `true`,
     * @param array $myBots ваш список ботов, если текущий устарел.
     * @return bool результат:
     * - `true`: если !HTTP_USER_AGENT(нету у разных спам ботов) или выявлен бот.
     * - `false` (default).
     * 
     * Можно выявить:
     * - `поисковых`: google, rambler, bing, yandex и др.
     * - `социальных`: facebook, Twitter, telegram, vk и др.
     * - `рекламных`: kontur, komodia, linkpad и др.
     * - `сканеры`: dot, SafeDNS, oBot и др.
     * 
     * Кому разрешить/запретить, решать Вам.
     * 
     * Запрещая всё и всем, Вы можете лишится продвижения сайта в поисковых ресурсах и/или красивых привью в соц сетях. 
     */
    public static function isBot(&$botname = '', $myBots = null)
    {
        // если есть USER_AGENT (нету у разных спам ботов)
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
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
                'SafeDNSBot', 'oBot', 'LinkpadBot', 'bingbot', 'Googlebot', 'CensysInspect', 'paloaltonetworks',
                '2ip', 'CMS Detector',
                // всякая фигня
                'libwww-perl', 'libwww', 'perl', 'zgrab', 'curl', 'ApiTool', 'masscan', 'Apache', 'Python', 'Java', 'Go',
                'HTTP Banner Detection', 'netsystemsresearch', 'gdnplus', 'fasthttp',
                // Другие
                'bot', 'bots', 'Bot', 'http', 'Http', '+http', '@'
            ];
            if ($myBots != null) $bots = array_merge($myBots, $bots);
            foreach ($bots as $bot) {
                if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                    $botname = $bot;
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * Delays execution of the script by the given time.
     * @param mixed $time Time to pause script execution. Can be expressed
     * as an integer or a decimal.
     * ```php
     * H::msleep(1.5); // delay for 1.5 seconds
     * H::msleep(.1); // delay for 100 milliseconds
     * ```
     */
    public static function msleep($time)
    {
        usleep($time * 1000000);
    }
}
