<?php

namespace Bermuda\Cycle;

use Bermuda\Config\ConfigProvider as AbstractProvider;
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

class ConfigProvider extends AbstractProvider
{
    public const configKey = 'cycle';
    protected function getFactories(): array
    {
        return [
            ORMInterface::class => static function (ContainerInterface $container): ORM {
                return new ORM($container->get(FactoryInterface::class), $container->get(SchemaInterface::class),
                    $container->has(CommandGeneratorInterface::class) ?
                        $container->get(CommandGeneratorInterface::class) : null,
                    $container->get(HeapInterface::class)
                );
            },
            DatabaseProviderInterface::class => static function (ContainerInterface $container): DatabaseManager {
                return new DatabaseManager($container->get(DatabaseConfig::class), $container->get(LoggerFactoryInterface::class));
            },
            LoggerFactoryInterface::class => static function (): LoggerFactoryInterface {
                return new class implements LoggerFactoryInterface {
                    public function getLogger(DriverInterface $driver = null): LoggerInterface
                    {
                        return new NullLogger;
                    }
                };
            },
            DatabaseConfig::class => static function (ContainerInterface $container): DatabaseConfig {
                $config = $container->get(self::containerConfigKey);
                if (!isset($config[self::configKey])) {
                    throw new \RuntimeException('Database configuration expected');
                }
                return new DatabaseConfig($config[self::configKey][0]);
            },
            HeapInterface::class => static fn(): Heap => new Heap,
            FactoryInterface::class => static function (ContainerInterface $container): Factory {
                return new Factory($container->get(DatabaseProviderInterface::class),
                    defaultCollectionFactory: $container->get(CollectionFactoryInterface::class)
                );
            },
            CollectionFactoryInterface::class => static function (): DoctrineCollectionFactory {
                return new DoctrineCollectionFactory();
            },
            SchemaInterface::class => static function (ContainerInterface $container): Schema {
                $config = $container->get(self::containerConfigKey);
                if (!isset($config[self::configKey])) {
                    throw new \RuntimeException('Database configuration expected');
                }
                return new Schema($config[self::configKey][1]);
            },
            EntityManagerInterface::class => static function (ContainerInterface $container): EntityManager {
                return new EntityManager($container->get(ORMInterface::class));
            },
        ];
    }
}
