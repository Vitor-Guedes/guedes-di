<?php

namespace GuedesDI;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class Container
{
    protected static $instance = null;

    protected $dependecies = [];

    public function __construct() { }

    public function __clone() { }

    /**
     * @return Container
     */
    public static function build()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param string $name
     * @param \Closuere $callback
     * 
     * @return Container
     */
    public function set(string $name, \Closure $callback) : Container
    {
        if ($this->has($name)) {
            return $this;
        }

        $this->dependecies[$name] = $this->resolve($callback);
        return $this;
    }

    /**
     * @param string $name
     * 
     * @return bool|mixed
     */
    public function get(string $name)
    {
        if ($this->has($name)) {
            return $this->dependecies[$name];
        }
        return false;
    }

    /**
     * @param string $name
     * 
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->dependecies[$name]);
    }

    /**
     * @param \Closure $callback
     * 
     * @return mixed
     */
    protected function resolve($callback)
    {
        $reflectionFunciton = new ReflectionFunction($callback);
        if ($parameters = $this->factoryParameters($reflectionFunciton)) {
            return $reflectionFunciton->invokeArgs($parameters);
        }
        return $reflectionFunciton->invoke();
    }

    /**
     * @param string $abstract
     * @param array $parameters
     * 
     * @return mixed
     */
    public function make(string $abstract, array $parameters = [])
    {
        return $this->factory($abstract, $parameters);
    }

    /**
     * @param string $abstract
     * 
     * @return mixed
     */
    public function makeSingleton(string $abstract)
    {
        return $this->factorySingleton($abstract);
    }

    /**
     * @param string $abstract
     * @param array $parameters
     * 
     * @return bool|mixed
     * 
     */
    protected function factory(string $abstract, array $parameters = [])
    {
        if (class_exists($abstract)) {
            $reflectionClass = $this->getReflectionClass($abstract);
            $construct = $reflectionClass->getConstructor();

            if (!$construct) {
                return $this->newInstance($abstract, false, $parameters);
            }

            return $this->newInstance($abstract, true, $this->factoryParameters($construct));
        }
        return false;
    }

    public function factorySingleton(string $abstract)
    {
        if (class_exists($abstract)) {
            return $abstract::getInstance();
        }
        return false;
    }

    protected function getReflectionClass(string $abstract)
    {
        return new ReflectionClass($abstract);
    }

    protected function newInstance(string $abstract, bool $withContructor = true, array $parameters)
    {
        $reflectionClass = new ReflectionClass($abstract);
        if ($withContructor) {
            return $reflectionClass->newInstanceArgs($parameters);
        }
        return $reflectionClass->newInstanceWithoutConstructor(); 
    }

    public function factoryParameters($construct)
    {
        $_parameters = [];
        $parameters = $construct->getParameters();
        if (count($parameters) > 0) {
            foreach ($parameters as $parameter) {
                $_parameters[$parameter->getName()] = $this->factory(
                    $parameter->getType()->getName()
                );
            }
        }
        return $_parameters;
    }
}