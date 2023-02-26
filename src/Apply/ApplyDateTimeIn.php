<?php

namespace Bermuda\Cycle\Apply;

use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Injection\Parameter;
use Cycle\Database\Query\SelectQuery;

final class ApplyDateTimeIn implements Selectable
{
    public function __construct(
        public readonly string $column,
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @param SelectQuery $query
     * @param SchemaInterface $schema
     * @param \DateTimeInterface[] $dates
     * @return SelectQuery
     */
    public function apply(SelectQuery $query, mixed $dates): SelectQuery
    {
        return $query->where(
            new Fragment("Date($this->column)"), 'in',
            new Parameter(array_map(fn(\DateTimeInterface $date) => $date->format($this->dateTimeFormat), $dates))
        );
    }
}
