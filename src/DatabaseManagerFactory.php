<?php

namespace Bermuda\Cycle;

use Cycle\ORM\Exception\ConfigException;
use Psr\Container\ContainerInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;

final class DatabaseManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return DatabaseManager
     */
    public function __invoke(ContainerInterface $container): DatabaseManager
    {
        $config = $container->get('config');

        if (!isset($config['cycle'])) {
            throw new ConfigException('Database configuration expected');
        }

        return new DatabaseManager(new DatabaseConfig($config['cycle'][0]));
    }
}
