<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;

function repository_factory(string $cls): callable
{
    return static function(ContainerInterface $container) use ($cls) {
        return $container->get(ORMInterface::class)->getRepository($cls);
    };
}
