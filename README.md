DoctrineDBALBridge
==================

A bridge for using [SimpleBus][1] with [Doctrine DBAL][4]. 

By [Jasper N. Brouwer](https://github.com/jaspernbrouwer)

## Versions

Version 1.x is compatible with [SimpleBus][1]/[CommandBus][2].

Version 2.x is compatible with [SimpleBus][1]/[MessageBus][3].

## Installation

Using Composer:

    composer require jaspernbrouwer/doctrine-dbal-bridge

## Usage

1. Set up a [command bus][3]:

    ```php
    $commandBus = ...;
    ```

2. Set up a [Doctrine DBAL][4] connection:

    ```php
    $connection = ...;
    ```

3. Set up the WrapsMessageHandlingInTransaction middleware:

    ```php
    use JNB\DoctrineDBALBridge\MessageBus\WrapsMessageHandlingInTransaction;
    
    $transactionalMiddleware = new WrapsMessageHandlingInTransaction($connection);
    ```

3. Add the middleware to the command bus: 

    ```php
    $commandBus->addMiddleware($transactionalMiddleware);
    ```

[1]: https://github.com/SimpleBus
[2]: https://github.com/SimpleBus/CommandBus
[3]: https://github.com/SimpleBus/MessageBus
[4]: http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/
