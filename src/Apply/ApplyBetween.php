<?php

namespace Bermuda\Cycle\Apply;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyBetween
{
    public function __construct(
        public readonly string $column,
    ) {
    }

    /**
     * @param Select $select
     * @param SchemaInterface $schema
     * @param array $value
     * @return Select
     */
    public function __invoke(Select $select, array $value): Select
    {
        return $select->where($this->column, 'between', $value[0], $value[1]);
    }
}
