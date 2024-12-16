<?php

namespace App\classes;

use Exception, Throwable;
use stdClass, Closure, ReflectionClass, ReflectionProperty;

/**
 * Class CloneObject
 * @property object $object
 * @method self createGetterAndSetter
 * @method array showAll
 */
class CloneObject
{
    /**
     * @var object
     */
    public $object;

    /**
     *
     * @param array|object $arguments
     */
    public function __construct($object = [])
    {
        $this->object = new stdClass();
        if (!empty($object)) {
            foreach ($object as $property => $argument) {
                $this->{$property} = $argument;
            }
            if (is_object($object)) {
                $this->object = clone $object;
            }
        }
    }

    /**
     * Вызов метода
     * @param string $method
     * @param mixed $arguments
     */
    public function __call($method, $arguments)
    {
        $arguments = array_merge(["stdObject" => $this], $arguments); // Note: method argument 0 will always referred to the main class ($this).
        if (method_exists($this, $method)) {
            return call_user_func_array($this->{$method}, $arguments);
        } else if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else if (method_exists($this->object, $method)) {
            return call_user_func_array($this->object->{$method}, $arguments);
        } else {
            throw new Exception("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }

    /**
     * Добавить динамическое свойство
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->{$property} = $value;
    }

    /**
     * Получить значение свойство
     * @param string $property
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        } else if (property_exists($this->object, $property)) {
            try {
                return $this->object->{$property};
            } catch (Throwable $th) { // protected or private
                $get = 'get' . ucfirst($property);
                if (method_exists($this->object, $get)) {
                    return $this->object->$get();
                } else {
                    return $this->reader($property);
                }
            }
        }
        return null;
    }

    /**
     * Получить значения приватных свойств
     * @param string $property
     * @param mixed $value
     * https://habr.com/ru/articles/186718/
     */
    private function reader($property)
    {
        $value = Closure::bind(function () use ($property) {
            return $this->$property;
        }, $this->object, $this->object)->__invoke();
        return $value;
    }

    /**
     * 
     * ```php
     * $clone = new CloneObject(['name'=>'test']);
     * $clone->name = 'test1';
     * $name = $clone->name; // test1
     * $test = $clone->test; // null
     * $clone->createGetterAndSetter();
     * $name = $clone->setName('test2')->getName();  // test2
     * //
     * $clone = new CloneObject(new Test());
     * $test = $clone->object; // raw Test class
     * $clone->createGetterAndSetter();
     * $test = $clone->getObject() // raw Test class
     * ```
     */
    public function createGetterAndSetter(): self
    {
        foreach ($this as $property => $value) {
            if (!$value instanceof Closure) {
                $this->{"set" . ucfirst($property)} = function ($stdObject, $value) use ($property) {  // Note: you can also use keyword 'use' to bind parent variables.
                    $stdObject->{$property} = $value;
                    return $stdObject;
                };
                $this->{"get" . ucfirst($property)} = function ($stdObject) use ($property) {  // Note: you can also use keyword 'use' to bind parent variables.
                    return $stdObject->{$property};
                };
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function showAll(): array
    {
        $output = [];
        // свойства этого класса
        foreach ($this as $property => $value) {
            if ($property != 'object' and !$value instanceof Closure) {
                $output[$property] = $value;
            }
        }
        // свойства клонированного класса
        $reflect = new ReflectionClass($this->object);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_STATIC | ReflectionProperty::IS_PRIVATE);
        foreach ($props as $property) {
            $output[$property->getName()] = $this->__get($property->getName());
        }
        return $output;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(';', $this->showAll());
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return $this->showAll();
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        $this->__construct($data);
    }
}
