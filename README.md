DoctrineDBALBridge
==================

A bridge for using [SimpleBus](https://github.com/SimpleBus) with [Doctrine DBAL](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/). 

By [Jasper N. Brouwer](https://github.com/jaspernbrouwer)

## Installation

Using Composer:

    composer require jaspernbrouwer/doctrine-dbal-bridge

## Usage

1. Set up a [command bus](https://github.com/SimpleBus/CommandBus):

    ```php
    $commandBus = ...;
    ```

2. Set up a Doctrine DBAL connection:

    ```php
    $connection = ...;
    ```

3. Wrap the existing command bus in order to handle your commands inside a database transaction: 

    ```php
    use JNB\DoctrineDBALBridge\CommandBus\WrapsNextCommandInTransaction;
    
    $transactionalCommandBus = new WrapsNextCommandInTransaction($connection);
    $transactionalCommandBus->setNext($commandBus);
    ```
