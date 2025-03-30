<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Query\SelectQuery;

final class ApplyBetween implements Selectable
{
    public function __construct(
        public readonly string $column,
    ) {
    }

    /**
     * @param SelectQuery $query
     * @param array $value
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $value): SelectQuery
    {
        return $query->where($this->column, 'between', $value[0], $value[1]);
    }
}
