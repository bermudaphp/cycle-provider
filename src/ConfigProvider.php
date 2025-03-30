<?php

namespace Bermuda\Cycle;

use Bermuda\Config\ConfigProvider as AbstractProvider;
use Bermuda\Cycle\Typecast\TypecastHandler;
use Bermuda\Cycle\Typecast\TypecastHandlerFactory;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\LoggerFactoryInterface;
use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Collection\CollectionFactoryInterface;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function Bermuda\Config\conf;

class ConfigProvider extends AbstractProvider
{
    public const string CONFIG_KEY = 'cycle';
    protected function getFactories(): array
    {
        return [
            ORMInterface::class => [ConfigProvider::class, 'createORM'],
            DatabaseProviderInterface::class => [ConfigProvider::class, 'createDatabaseManager'],
            LoggerFactoryInterface::class => [ConfigProvider::class, 'createLoggerFactory'],
            DatabaseConfig::class => [ConfigProvider::class, 'createDatabaseConfig'],
            HeapInterface::class => [ConfigProvider::class, 'createHeap'],
            FactoryInterface::class => [ConfigProvider::class, 'createFactory'],
            CollectionFactoryInterface::class => [ConfigProvider::class, 'createCollectionFactory'],
            SchemaInterface::class => [ConfigProvider::class, 'createSchema'],
            EntityManagerInterface::class => [ConfigProvider::class, 'createEntityManager'],
            TypecastHandler::class => TypecastHandlerFactory::class
        ];
    }

    public static function createORM(ContainerInterface $container): ORM
    {
        return new ORM($container->get(FactoryInterface::class), $container->get(SchemaInterface::class),
            $container->has(CommandGeneratorInterface::class) ?
                $container->get(CommandGeneratorInterface::class) : null,
            $container->get(HeapInterface::class)
        );
    }

    public static function createDatabaseManager(ContainerInterface $container): DatabaseManager
    {
        return new DatabaseManager($container->get(DatabaseConfig::class), $container->get(LoggerFactoryInterface::class));
    }

    public static function createLoggerFactory(ContainerInterface $container): LoggerFactoryInterface
    {
        return new class implements LoggerFactoryInterface {
            public function getLogger(?DriverInterface $driver = null): LoggerInterface
            {
                return new NullLogger;
            }
        };
    }

    public static function createDatabaseConfig(ContainerInterface $container): DatabaseConfig
    {
        $config = conf($container);
        if (!isset($config[self::CONFIG_KEY])) {
            throw new \RuntimeException('Database configuration expected');
        }
        return new DatabaseConfig($config[self::CONFIG_KEY][0]->toArray());
    }

    public static function createHeap(ContainerInterface $container): Heap
    {
        return new Heap();
    }

    public static function createFactory(ContainerInterface $container): Factory
    {
        return new Factory($container->get(DatabaseProviderInterface::class),
            defaultCollectionFactory: $container->get(CollectionFactoryInterface::class)
        );
    }

    public static function createCollectionFactory(ContainerInterface $container): CollectionFactoryInterface
    {
        return new DoctrineCollectionFactory();
    }

    public static function createSchema(ContainerInterface $container): Schema
    {
        $config = conf($container);
        if (!isset($config[self::CONFIG_KEY])) {
            throw new \RuntimeException('Database configuration expected');
        }

        return new Schema($config[self::CONFIG_KEY][1]);
    }

    public static function createEntityManager(ContainerInterface $container): EntityManager
    {
        return new EntityManager($container->get(ORMInterface::class));
    }
}
