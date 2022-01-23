<?php

namespace Bermuda\Cycle;

use Cycle\ORM\Schema;
use Psr\Container\ContainerInterface;

final class SchemaFactory
{
    public function __invoke(ContainerInterface $container): Schema
    {
        return new Schema($container->get('config')['cycle'][1] ?? []);
    }
}
