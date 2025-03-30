<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Parameter;
use Cycle\Database\Query\SelectQuery;

final class ApplyIn implements Selectable
{
    public function __construct(
        public readonly string $column
    ) {
    }

    /**
     * @param SelectQuery $select
     * @param array $values
     * @return SelectQuery
     */
    public function apply(SelectQuery $select, mixed $values): SelectQuery
    {
        return $select->where($this->column, 'in', new Parameter($values));
    }
}
