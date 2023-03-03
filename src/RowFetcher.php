<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\SchemaInterface;

class RowFetcher
{
    protected CastableInterface $handler;

    public function __construct(
        public readonly string $role,
        public readonly ORMInterface $orm
    ) {
        $handler = $orm->getSchema()->define($role, SchemaInterface::TYPECAST_HANDLER);
        $this->handler = new $handler($orm->getSource($role)->getDatabase());
        $this->handler->setRules($orm->getSchema()->define($role, SchemaInterface::TYPECAST));
    }

    public function fetch(array $row): array
    {
        return $this->handler->cast(array_filter($row, static fn($v) => $v !== null));
    }
}
