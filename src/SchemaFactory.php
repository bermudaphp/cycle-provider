<?php

namespace Bermuda\Provider;


use Cycle\ORM\Schema;
use Psr\Container\ContainerInterface;


/**
 * Class SchemaFactory
 * @package Bermuda\Provider
 */
final class SchemaFactory
{
    /**
     * @param ContainerInterface $container
     * @return Schema
     */
    public function __invoke(ContainerInterface $container): Schema
    {
        return new Schema($container->get('config')['cycle'][1] ?? []);
    }
}
