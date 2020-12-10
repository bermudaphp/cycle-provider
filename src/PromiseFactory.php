<?php

namespace App;


use Psr\Container\ContainerInterface;


/**
 * Class PromiseFactory
 * @package App
 */
final class PromiseFactory
{
    /**
     * @param ContainerInterface $container
     * @return \Cycle\ORM\Promise\PromiseFactory
     */
    public function __invoke(ContainerInterface $container): \Cycle\ORM\Promise\PromiseFactory
    {
        return new \Cycle\ORM\Promise\PromiseFactory();
    }
}