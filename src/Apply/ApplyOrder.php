<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Query\SelectQuery;

final class ApplyOrder implements Selectable
{
    public function __construct(
        public readonly string $column,
        public readonly OrderMode $mode = OrderMode::Asc
    ) {
    }

    /**
     * @param SelectQuery $query
     * @param mixed|null $vaule
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $vaule = null): SelectQuery
    {
        return $query->orderBy($this->column, strtoupper($this->mode->name));
    }
}
