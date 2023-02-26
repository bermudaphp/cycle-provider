<?php

namespace Bermuda\Cycle;

use Cycle\Database\Query\SelectQuery;

interface Selectable
{
    /**
     * @throws \InvalidArgumentException
     */
    public function apply(SelectQuery $query, mixed $value): SelectQuery ;
}
