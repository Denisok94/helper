<?php

namespace denisok94\helper\other;

/**
 * class Session
 * 
 * ```php
 * $session = (new Session())->start();
 * 
 * if (!$session->has('counter')) {
 *     $session->set('counter', 0);
 * }
 * 
 * $counter = $session->get('counter');
 * $session->set('counter', ++$counter);
 * 
 * $session->setArray([
 *    'one' => 1,
 *    'two' => 2,
 *    'three' => 3
 * ]);
 * print_r([$session,  $session->get('counter'),  $session->get('two'),]);
 * 
 * $data = $session->encode();
 * $session->destroy();
 * 
 * $session2 = (new Session())->start()->decode($data);
 * print_r([$data, $session2->encode(), Session::unserialize($data)]);
 * ```
 */
class Session
{
    /**
     * @var int|null
     */
    private $cookieTime;

    /**
     * задаем время жизни сессионных кук
     * @param string $cookieTime
     */
    public function __construct(string $cookieTime = '+30 days')
    {
        $this->cookieTime = strtotime($cookieTime);
        session_cache_limiter(false);
    }

    /**
     * @return string|false
     */
    public static function getId()
    {
        return session_id();
    }

    /**
     * Задать id сессии
     * @param string|null $session_id
     * @return self
     * 
     * ```php
     * $session = (new Session())
     *  ->setId('edmjg1vp3vq0sqn6ag5ftqg9tf')
     *  ->start(true);
     * ```
     * 
     */
    public function setId(?string $session_id = null): self
    {
        if ($session_id && $this->session_valid_id($session_id)) {
            session_id($session_id);
        }
        return $this;
    }

    private function session_valid_id(string $session_id)
    {
        return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $session_id) > 0;
    }

    /**
     * стартуем сессию
     * @param boolean $start
     * @return self
     * ```php
     * $session = new Session();
     * $session->start();
     * $session = (new Session())->start();
     * ```
     */
    public function start(bool $start = false): self
    {
        if (!self::getId() || $start) session_start();
        return $this;
    }

    /**
     * https://web-answers.ru/php/php-sessija-na-poddomenah.html
     * @return self
     */
    public function startCrossPodDomain()
    {
        $currentCookieParams = session_get_cookie_params();

        $serverParts = explode('.', $_SERVER['HTTP_HOST']);
        $serverPartsCount = sizeof($serverParts);

        if ($serverPartsCount > 1) {
            $serverName = '.' . $serverParts[$serverPartsCount - 2] . '.' . $serverParts[$serverPartsCount - 1];

            setcookie('serverParts', $serverName, time() + 86400, '/');

            session_set_cookie_params(
                time() + 315360000, //(10 * 365 * 24 * 60 * 60),
                '/',
                $serverName, // equates to '.myDomain.com'
                FALSE,
                $currentCookieParams["httponly"]
            );
        }

        $visited = $this->start()->get('visitedDomains', []);
        $visited[$_SERVER['HTTP_HOST']] = 'visited';

        $this->set('visitedDomains', $visited);
        return $this;
    }

    /**
     * Проверяем сессию на наличие в ней переменной c заданным именем
     */
    public function has(string $name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Устанавливаем сессию с именем $name и значением $value
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set(string $name, $value): self
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    /**
     * Когда мы хотим сохранить в сессии сразу много значений - используем массив
     *
     * @param array $vars
     * @return self
     */
    public function setArray(array $vars): self
    {
        foreach ($vars as $name => $value) {
            $this->set($name, $value);
        }
        return $this;
    }

    /**
     * Получаем значение сессий
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $_SESSION[$name] ?? $default;
    }

    /**
     * @param string $name - Уничтожаем сессию с именем $name
     */
    public function unset(string $name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Полностью очищаем все данные
     */
    public function destroy()
    {
        if (self::getId()) session_destroy();
    }

    /**
     * Полностью данные сессии
     * @return string
     * 
     * ```php
     * $data = Session::encode();
     *  // ~'login_ok|b:1;nome|s:4:"sica";inteiro|i:34;'
     * ```
     */
    public static function encode()
    {
        return session_encode();
    }

    /**
     * Задать данные сессии
     * @param string $data
     * @return self
     * ```php
     * $session = (new Session())->start()->decode($data);
     * ```
     */
    public function decode(string $data)
    {
        if (self::getId()) session_decode($data);
        return $this;
    }

    /**
     * Распарсить данные сессии
     * @param string $session_data
     * 
     * ```php
     * Session::unserialize(Session::encode());
     * Session::unserialize('login_ok|b:1;nome|s:4:"sica";inteiro|i:34;');
     * Session::unserialize(session_encode());
     * ```
     * @return array
     * @throws
     */
    public static function unserialize(string $session_data)
    {
        $method = ini_get("session.serialize_handler");
        switch ($method) {
            case "php":
                return self::unserialize_php($session_data);
                break;
            case "php_binary":
                return self::unserialize_phpbinary($session_data);
                break;
            default:
                throw new \Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }

    /**
     * @param string $session_data
     * @return array
     * @throws
     */
    private static function unserialize_php(string $session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new \Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    /**
     *
     * @param string  $session_data
     * @return array
     */
    private static function unserialize_phpbinary(string $session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            $num = ord($session_data[$offset]);
            $offset += 1;
            $varname = substr($session_data, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    //-------------------------------------

    /**
     * 
     */
    public function hasCookie(string $name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Устанавливаем куки  
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setCookie(string $name, $value)
    {
        setcookie($name, $value, $this->cookieTime);
        return $this;
    }

    /**
     * Получаем куки
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie(string $name)
    {
        return $_COOKIE[$name];
    }

    /**
     * @param string $name Удаляем
     */
    public function removeCookie(string $name)
    {
        setcookie($name, null);
    }
}
