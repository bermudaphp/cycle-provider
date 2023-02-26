<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Query\SelectQuery;

final class ApplyLoad implements Selectable
{
    /**
     * @param SelectQuery $query
     * @param array $relations
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $relations): SelectQuery
    {
        return $query->load($relations);
    }
}
