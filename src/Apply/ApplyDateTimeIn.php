<?php

namespace App\Query\Applies;

use Cycle\Database\Injection\Fragment;
use Cycle\Database\Injection\Parameter;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyDateTimeIn
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
            new Fragment("Date($this->column)"), 'in',
            new Parameter(array_map(fn(\DateTimeInterface $date) => $date->format($this->dateTimeFormat), $dates))
        );
    }
}