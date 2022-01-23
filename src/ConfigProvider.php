<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\PromiseFactoryInterface;
use Cycle\ORM\SchemaInterface;
use Spiral\Database\DatabaseManager;
use Cycle\ORM\EntityManager;

class ConfigProvider extends \Bermuda\Config\ConfigProvider
{
    protected function getFactories(): array
    {
        return [
            EntityManagerInterface::class => static fn(ContainerInterface $container) => new EntityManager($container->get(ORMInterface::class));
            DatabaseManager::class => DatabaseManagerFactory::class,
            SchemaInterface::class => SchemaFactory::class,
            ORMInterface::class => ORMFactory::class
        ];
    }
}
