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
        return $this->has($name) ? $this->dependecies[$name] : false;
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
                return $this->newInstance($reflectionClass, false, $parameters);
            }

            return $this->newInstance($reflectionClass, true, $this->factoryParameters($construct));
        }
        return false;
    }

    /**
     * @param string $abstract
     * 
     * @return bool
     */
    public function factorySingleton(string $abstract)
    {
        return class_exists($abstract) ? $abstract::getInstance() : false;
    }

    /**
     * @param string $abstract
     * 
     * @return ReflectionClass
     */
    protected function getReflectionClass(string $abstract) : ReflectionClass
    {
        return new ReflectionClass($abstract);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param bool $withContructor
     * @param array $parameters
     * 
     * @return mixed
     */
    protected function newInstance(ReflectionClass $reflectionClass, bool $withContructor = true, array $parameters = [])
    {
        if ($withContructor) {
            return $reflectionClass->newInstanceArgs($parameters);
        }
        return $reflectionClass->newInstanceWithoutConstructor(); 
    }

    /**
     * @param ReflectionMethod|\Closure $method
     * 
     * @return array
     */
    protected function factoryParameters($method)
    {
        $parameters = [];
        foreach ($method->getParameters() ?? [] as $parameter) {
            $parameters[$parameter->getName()] = $this->factory(
                $parameter->getType()->getName()
            );
        }
        return $parameters;
    }
}