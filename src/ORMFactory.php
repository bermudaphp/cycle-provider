<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORM;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Database\DatabaseManager;
use Cycle\ORM\Config\RelationConfig;
use Psr\Container\ContainerInterface;
use Cycle\ORM\PromiseFactoryInterface;

final class ORMFactory
{
    /**
     * @param ContainerInterface $container
     * @return ORM
     */
    public function __invoke(ContainerInterface $container): ORM
    {
        return (new ORM(new Factory($container->get(DatabaseManager::class),
                RelationConfig::getDefault(), cget($container, FactoryInterface::class),
                $container
            )
        ))
            ->withSchema($container->get(SchemaInterface::class))
            ->withPromiseFactory($container->get(PromiseFactoryInterface::class));
    }
}
