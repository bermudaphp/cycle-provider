<?php

namespace App;


use Cycle\ORM\Schema;
use Psr\Container\ContainerInterface;


/**
 * Class SchemaFactory
 * @package App
 */
final class SchemaFactory
{
    /**
     * @param ContainerInterface $container
     * @return Schema
     */
    public function __invoke(ContainerInterface $container): Schema
    {
        $config = $container->get('config');
        return new Schema($config['cycle']['schema'] ?? []);
    }
}