<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Query\SelectQuery;

final class ApplyOffset implements Selectable
{
    /**
     * @param SelectQuery $query
     * @param int $offset
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $offset): SelectQuery
    {
        return $query->offset($offset);
    }
}
