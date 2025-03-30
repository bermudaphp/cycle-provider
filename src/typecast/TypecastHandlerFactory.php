<?php

namespace Bermuda\Cycle\Typecast;

use Psr\Container\ContainerInterface;
use function Bermuda\Config\conf;

final class TypecastHandlerFactory
{
    const string TYPECAST_RULES_CONFIG_KEY = 'Bermuda\Cycle\Typecast:rules';

    public function __invoke(ContainerInterface $container): TypecastHandler
    {
        return new TypecastHandler(conf($container)->get(self::TYPECAST_RULES_CONFIG_KEY, []));
    }
}
