<?php

namespace Bermuda\Cycle;


use PhpParser\Parser;
use Cycle\ORM\Promise\Printer;
use Cycle\ORM\Promise\Traverser;
use Cycle\ORM\Promise\ProxyFactory;
use Psr\Container\ContainerInterface;
use Doctrine\Instantiator\Instantiator;
use Cycle\ORM\Promise\Declaration\Extractor;


/**
 * Class PromiseFactory
 * @package Bermuda\Cycle
 */
final class PromiseProxyFactory
{
    public function __invoke(ContainerInterface $container): ProxyFactory
    {
        return new ProxyFactory($container->get(Extractor::class),
            $container->get(Printer::class), new Instantiator()
        );
    }

    /**
     * @param ContainerInterface $container
     * @return Extractor
     */
    private function getExtractor(ContainerInterface $container): Extractor
    {
        if ($container->has(Extractor::class))
        {
            return $container->get(Extractor::class);
        }

        return new Extractor(
            new Extractor\Constants(),
            new Extractor\Properties(),
            new Extractor\Methods(new Traverser(), $this->getParser($container)),
        );
    }

    /**
     * @param ContainerInterface $container
     * @return Parser|null
     */
    private function getParser(ContainerInterface $container):? Parser
    {
        return $container->has(Parser::class) ?
            $container->get(Parser::class) : null;
    }
}
