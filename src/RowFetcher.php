<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\SchemaInterface;

class RowFetcher implements RowFetcherInterface
{
    protected array $fetchers = [];
    protected CastableInterface $handler;
    
    public function __construct(
        public readonly string $role,
        public readonly ORMInterface $orm,
        array $exceptedRules = []
    ) {
        $handler = $orm->getSchema()->define($role, SchemaInterface::TYPECAST_HANDLER);
        $this->handler = new $handler($orm->getSource($role)->getDatabase());

        $rules = $orm->getSchema()->define($role, SchemaInterface::TYPECAST);
        if ($exceptedRules !== []) {
            $rules = array_diff_key($rules, array_flip($exceptedRules));
        }

        $this->handler->setRules($rules);
    }

    public function fetch(array $row): array
    {
        $row = $this->handler->cast(array_filter($row, static fn($v) => $v !== null));
        foreach ($this->fetchers as $fetcher) $row = $fetcher->fetch($row);

        return $row;
    }
    
    public function add(RowFetcherInterface $fetcher): static
    {
        $this->fetchers[] = $fetcher;
        return $this;
    }
}
