<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Query\SelectQuery;

final class ApplyLimit implements Selectable
{
    /**
     * @param SelectQuery $query
     * @param int $limit
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $limit): SelectQuery
    {
        return $query->limit($limit);
    }
}
