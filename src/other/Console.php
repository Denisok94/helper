<?php

namespace denisok94\helper\other;

use \denisok94\helper\Helper as H;

if (!function_exists('str_starts_with')) {
    function str_starts_with(?string $haystack, ?string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}

/**
 * class Console
 * 
 * ```php
 * // php console.php arg1 arg2=val -o -a5 --option --option1=6 --option1=3
 * $console = new Console();
 * $console->getArguments(); // [arg1,arg2=>val]
 * $console->getArgument(0); // arg1
 * $console->getArgument('arg2'); // val
 * $console->getOptions(); // [o=>null,a=>5,option=>null,option1=>[6,3]]
 * // set default value 'true'
 * $console2 = new Console(null, true);
 * $console2->getOptions(); // [o=>true,option=>true,...]
 * // required parameters
 * try {
 *  $console3 = new Console([
 *   'test', // required arguments and/or options
 *   'options' => ['test', ],  // required options
 *   'arguments' => ['test', ],  // required arguments
 *  ]);
 * } catch (\Exception $th) {
 *  die($th->getMessage());
 * }
 * ```
 * 
 * show():
 * ```php
 * for ($x = 1; $x <= 100; $x++) {
 *  Console::show($x);
 *  usleep(100000);
 * }
 * ```
 */
class Console
{
    private $token;
    private $parsed;
    private $options = [];
    private $arguments = [];
    private $defaultValue;
    /**
     * @var array|null
     */
    private $required;

    /**
     * $argv An array of parameters from the CLI (in the argv format)
     * 
     * @param array|null $required todo
     * @param mixed $defaultOptionsValue
     * @throws \Exception
     */
    public function __construct(?array $required = null, $defaultOptionsValue = null)
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];

        // strip the application name
        array_shift($argv);

        $this->token = $argv;
        $this->required = $required;
        $this->defaultValue = $defaultOptionsValue;
        $this->parse();
    }

    /**
     * @throws \Exception
     */
    private function parse()
    {
        $parseOptions = true;
        $this->parsed = $this->token;
        while (null !== $token = array_shift($this->parsed)) {
            if ($parseOptions && '' == $token) {
                $this->parseArgument($token);
            } elseif ($parseOptions && '--' == $token) {
                $parseOptions = false;
            } elseif ($parseOptions && str_starts_with($token, '--')) {
                $this->parseLongOption($token);
            } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
                $this->parseShortOption($token);
            } else {
                $this->parseArgument($token);
            }
        }
        $this->getRequired();
    }

    /**
     * @throws \Exception
     */
    private function getRequired()
    {
        if ($this->required != null) {
            $errors = [];

            if (isset($this->required['options']) && is_array($this->required['options'])) {
                foreach ($this->required['options'] as $option) {
                    if (!$this->hasOption($option)) $errors['options'][] = $option;
                }
                unset($this->required['options']);
            }
            if (isset($this->required['arguments']) && is_array($this->required['arguments'])) {
                foreach ($this->required['arguments'] as $argument) {
                    if (!$this->hasArgument($argument)) $errors['arguments'][] = $argument;
                }
                unset($this->required['arguments']);
            }
            if (count($this->required) > 0) {
                foreach ($this->required as $get) {
                    if ($this->get($get, 'required') == 'required') $errors['get'][] = $get;
                }
            }

            if (count($errors) > 0) {
                $error = "required parameters: ";
                $error .= isset($errors['options']) ? 'options: ' . H::implodeWrap(', ', $errors['options'], "'") . '. ' : '';
                $error .= isset($errors['arguments']) ? 'arguments: ' . H::implodeWrap(', ', $errors['arguments'], "'") . '. ' : '';
                $error .= isset($errors['get']) ? H::implodeWrap(', ', $errors['get'], "'") . '. ' : '';
                throw new \Exception($error);
            }
        }
    }

    //------------------------------------------------------------

    /**
     *
     * @param string $name
     * @return bool 
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Gets the array of options.
     * @return array
     * 
     * ```php
     * // php console.php -o -a5 --option --option1=6 --option1=3
     * $console = new Console();
     * $console->getOptions(); 
     * // [o=>null,a=>5,option=>null,option1=>[6,3]]
     * $console2 = new Console([], true);
     * $console2->getOptions(); 
     * // [o=>true,option=>true,...]
     * ```
     * 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns an option by name.
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    //------------------------------

    /**
     * Parses a short option.
     * @param string $token
     */
    private function parseShortOption($token)
    {
        $name = substr($token, 1);

        if (\strlen($name) > 1) {
            $this->addOption($name[0], substr($name, 1));
        } else {
            $this->addOption($name, $this->defaultValue);
        }
    }

    /**
     * Parses a long option.
     * @param string $token
     */
    private function parseLongOption($token)
    {
        $option = explode("=", substr($token, 2), 2);
        $this->addOption($option[0], $option[1] ?? $this->defaultValue);
    }

    /**
     * Adds a option value.
     * @param string $name
     * @param mixed $value
     */
    private function addOption($name, $value)
    {
        if ($this->hasOption($name)) {
            $option = $this->getOption($name);
            if (is_array($option)) {
                $this->options[$name][] = $value;
            } else {
                $this->options[$name] = [$option, $value];
            }
        } else {
            $this->options[$name] = $value;
        }
    }

    //------------------------------------------------------------

    /**
     * Returns true if an arguments exists by name or position.
     * @param string|int $name The arguments name or position
     * @return bool true if the arguments exists, false otherwise
     */
    public function hasArgument($name)
    {
        $arguments = \is_int($name) ? array_values($this->arguments) : $this->arguments;
        return isset($arguments[$name]);
    }

    /**
     * 
     * @return array
     * 
     * ```php
     * // php console.php arg1 arg2=val
     * $console = new Console();
     * $console->getArguments(); // [arg1,arg2=>val]
     * ```
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     *
     * @param string|int $name
     * @param mixed $default
     * @return mixed
     * 
     * ```php
     * // php console.php arg1 arg2=val
     * $console = new Console();
     * $console->getArguments(); // [arg1,arg2=>val]
     * $console->getArgument(0); // arg1
     * $console->getArgument('arg2'); // val
     * ```
     */
    public function getArgument($name, $default = null)
    {
        $arguments = \is_int($name) ? array_values($this->arguments) : $this->arguments;
        return $arguments[$name] ?? $default;
    }

    //------------------------------

    /**
     * Parses an argument.
     * @param string $token
     */
    private function parseArgument(string $token)
    {
        if (2 == count($a = explode("=", $token))) {
            $this->arguments[$a[0]] = $a[1];
        } else {
            $this->arguments[] = $token;
        }
    }

    //------------------------------------------------------------

    /**
     *
     * @param string|int $name argument or option
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $arguments = \is_int($name) ? array_values($this->arguments) : $this->arguments;
        return $arguments[$name] ?? ($this->options[$name] ?? $default);
    }

    /**
     * Статус в консоли
     * @param string $echo
     * @return string
     * ```php
     * for ($x = 1; $x <= 100; $x++) {
     *  Console::show($x);
     *  usleep(100000);
     * }
     * ```
     */
    public static function show(string $echo)
    {
        echo "\r$echo ";
        flush();
    }
}
