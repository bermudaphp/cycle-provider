<?php

namespace Bermuda\Cycle\Apply;

use Cycle\Database\Query\SelectQuery;

final class ApplyBetween implements Selectable
{
    public function __construct(
        public readonly string $column,
    ) {
    }
    
    public function apply(SelectQuery $query, mixed $value): Select
    {
        return $query->where($this->column, 'between', $value[0], $value[1]);
    }
}
