<?php

namespace GuedesDI\Tests\Unit;

use GuedesDI\Container;
use GuedesDI\Tests\Objects\Account;
use GuedesDI\Tests\Objects\Connection;
use GuedesDI\Tests\Objects\User;
use GuedesRouter\RouterCollection;
use PHPUnit\Framework\TestCase;

class DITest extends TestCase
{
    public function test_must_add_a_dependency_to_the_container()
    {
        $container = Container::build();

        $container->set('router', function () {
            return new RouterCollection();
        });

        $this->assertEquals(true, $container->has('router'));
    }

    public function test_must_return_an_instance_of_what_was_set()
    {
        $container = Container::build();

        $container->set('router', function () {

            return new RouterCollection();
        });

        $this->assertInstanceOf(
            RouterCollection::class, 
            $container->get('router')
        );
    }

    public function test_must_be_able_to_create_an_instance_and_its_dependencies()
    {
        $container = Container::build();

        $user = $container->make(User::class);

        $this->assertInstanceOf(
            User::class,
            $user
        );
        $this->assertInstanceOf(
            Account::class,
            $user->getAccount()
        );
    }

    public function test_must_resolve_a_callback_with_parameters()
    {
        $container = Container::build();

        $container->set('connection', function (Connection $connection) {
            return $connection;
        });

        $this->assertInstanceOf(
            Connection::class,
            $container->get('connection')
        );
    }

    public function test_should_return_the_same_singleton_instance()
    {
        $container = Container::build();

        $container->set('connection', function (Connection $connection) {
            return $connection->getInstance();
        });

        $firstConnection = $container->get('connection');
        $secondConnection = $container->get('connection');

        $this->assertEquals($firstConnection, $secondConnection);
    }

    public function test_must_create_singleton_class_and_return_the_same_instance_twice()
    {
        $container = Container::build();

        $firstConnection = $container->makeSingleton(Connection::class);
        $firstConnection->setConnectionName('mysql');
        $secondConnection = $container->makeSingleton(Connection::class);

        $this->assertEquals($firstConnection, $secondConnection);
        $this->assertEquals(
            $firstConnection->getConnectionName(), 
            $secondConnection->getConnectionName()
        );
    }
}