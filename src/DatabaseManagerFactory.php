<?php

namespace Bermuda\Provider;


use Psr\Container\ContainerInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;


/**
 * Class DatabaseManagerFactory
 * @package Bermuda\Provider
 */
final class DatabaseManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DatabaseManager
     */
    public function __invoke(ContainerInterface $container): DatabaseManager
    {
        return new DatabaseManager(new DatabaseConfig($container->get('config')['cycle']['config']));
    }
}
