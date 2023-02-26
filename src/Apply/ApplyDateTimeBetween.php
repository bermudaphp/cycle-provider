<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Query\SelectQuery;

final class ApplyDateTimeBetween implements Selectable
{
    public function __construct(
        public readonly string $column,
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @param SelectQuery $query
     * @param \DateTimeInterface[] $dates
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $dates): SelectQuery
    {
        return $query->where(
            new Fragment("Date($this->column)"),
            'between', $dates[0]->format($this->dateTimeFormat),
            $dates[1]->format($this->dateTimeFormat),
        );
    }
}
