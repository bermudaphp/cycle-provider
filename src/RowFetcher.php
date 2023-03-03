<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\SchemaInterface;

class RowFetcher
{
    protected array $columns;
    protected CastableInterface $handler;

    public function __construct(
        public readonly string $role,
        public readonly ORMInterface $orm
    ) {
        $handler = $orm->getSchema()->define($role, SchemaInterface::TYPECAST_HANDLER);
        $this->handler = new $handler($orm->getSource($role)->getDatabase());
        $this->handler->setRules($orm->getSchema()->define($role, SchemaInterface::TYPECAST));
        $this->columns = array_flip($orm->getSchema()->define($role, SchemaInterface::COLUMNS));
    }

    public function fetch(array $row): array
    {
        $columns = $this->columns;

        foreach ($row as $column => $value) {
            if (isset($columns[$column]) && !is_numeric($columns[$column])) $results[$columns[$column]] = $value;
            else $results[$column] = $value;
        }

        return array_filter($this->handler->cast($results ?? []), static fn($v) => $v !== null);
    }
}
