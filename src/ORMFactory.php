<?php

namespace Bermuda\Provider;


use Cycle\ORM\ORM;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Database\DatabaseManager;
use Cycle\ORM\Config\RelationConfig;
use Psr\Container\ContainerInterface;
use Cycle\ORM\PromiseFactoryInterface;


/**
 * Class ORMFactory
 * @package Bermuda\Provider
 */
final class ORMFactory
{
    /**
     * @param ContainerInterface $container
     * @return ORM
     */
    public function __invoke(ContainerInterface $container): ORMInterface
    {
        return (new ORM(new Factory($container->get(DatabaseManager::class),
                RelationConfig::getDefault(), $this->getSpiralFactory($container),
                $container
            )
        ))
            ->withSchema($container->get(SchemaInterface::class))
            ->withPromiseFactory($container->get(PromiseFactoryInterface::class));
    }
    
    /**
     * @param ContainerInterface $container
     * @return FactoryInterface|null
     */
    private function getSpiralFactory(ContainerInterface $container):? FactoryInterface
    {
        return $container->has(FactoryInterface::class) ?
            $container->get(FactoryInterface::class) : null;
    }
}
