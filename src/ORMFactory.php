<?php

namespace Bermuda\Provider;


use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Spiral\Core\FactoryInterface;
use Cycle\ORM\Config\RelationConfig;
use Psr\Container\ContainerInterface;


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
                $this->getRelationConfig(),
                $this->getSpiralFactory(),
                $container
            )
        ))
            ->withSchema($container->get(SchemaInterface::class))
            ->withPromiseFactory($container->get(PromiseFactoryInterface::class));
    }

    /**
     * @param ContainerInterface $container
     * @return RelationConfig|null
     */
    private function getRelationConfig(ContainerInterface $container):? RelationConfig
    {
        return $container->has(RelationConfig::class) ?
            $container->get(RelationConfig::class) : null;
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
