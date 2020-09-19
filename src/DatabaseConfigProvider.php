<?php


namespace Bermuda\Provider;


use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\Factory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\Printer;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Promise\ProxyFactory;
use Spiral\Database\DatabaseManager;
use Cycle\ORM\PromiseFactoryInterface;
use Doctrine\Instantiator\Instantiator;
use Spiral\Database\Config\DatabaseConfig;
use Cycle\ORM\Promise\Declaration\Extractor;
use Psr\Container\ContainerInterface;


class DatabaseConfigProvider
{
    public function __invoke()
    {
        return 
        [
            'dependencies' => 
            [
                'factories' => 
                [
                     DatabaseManager::class => static function(ContainerInterface $c)
                     {
                         $config = new DatabaseConfig($c->get('config')['database']['config']);
                         return new DatabaseManager($config);
                     },

                     SchemaInterface::class => static function(ContainerInterface $c)
                     {
                         return new Schema($c->get('config')['database']['schema']);
                     },

                     PromiseFactoryInterface::class => static function(ContainerInterface $container)
                     {
                         return new ProxyFactory($container->get(Extractor::class), 
                            $container->get(Printer::class), new Instantiator()
                         );
                     },

                     ORMInterface::class => static function(ContainerInterface $c)
                     {
                         return (new ORM(new Factory($c->get(DatabaseManager::class))))
                             ->withSchema($c->get(SchemaInterface::class))
                             ->withPromiseFactory($c->get(PromiseFactoryInterface::class));
                     }
               ]
            ]
        ];
    }
}
