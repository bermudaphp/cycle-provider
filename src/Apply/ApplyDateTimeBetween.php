<?php

namespace Bermuda\Cycle\Apply;

use Cycle\Database\Injection\Fragment;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyDateTimeBetween
{
    public function __construct(
        public readonly string $column,
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @param Select $select
     * @param SchemaInterface $schema
     * @param \DateTimeInterface[] $dates
     * @return Select
     */
    public function __invoke(Select $select, SchemaInterface $schema, array $dates): Select
    {
        return $select->where(
            new Fragment("Date($this->column)"),
            'between', $dates[0]->format($this->dateTimeFormat),
            $dates[1]->format($this->dateTimeFormat),
        );
    }
}
