<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Query\SelectQuery;

final class ApplyDateTime implements Selectable
{
    public function __construct(
        public readonly string $column,
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @param SelectQuery $query
     * @param \DateTimeInterface $date
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $date): SelectQuery
    {
        return $query->where(new Fragment("Date($this->column)"), $date->format($this->dateTimeFormat));
    }
}
