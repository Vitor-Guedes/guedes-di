<?php

namespace GuedesDI\Tests\Objects;

class Connection
{
    public static $instance = null;

    protected $connectionName;

    public function __construct() { }

    public function __clone() { }

    public function __wakeup() { }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function setConnectionName(string $name)
    {
        $this->connectionName = $name;
    }

    public function getConnectionName()
    {
        return $this->connectionName;
    }
}