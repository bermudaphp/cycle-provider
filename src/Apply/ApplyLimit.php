<?php

namespace Bermuda\Cycle\Apply;

use Cycle\Database\Query\SelectQuery;

final class ApplyLimit implements Selectable
{
    public function apply(SelectQuery $query, mixed $limit): SelectQuery
    {
        return $query->limit($limit);
    }
}
