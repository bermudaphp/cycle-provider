<?php

namespace Bermuda\Cycle\Appy;

use Cycle\Database\Injection\Parameter;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyIn
{
    public function __construct(
        public readonly string $column
    ) {
    }

    /**
     * @param Select $select
     * @param SchemaInterface $schema
     * @param array $values
     * @return Select
     */
    public function __invoke(Select $select, SchemaInterface $schema, array $values): Select
    {
        return $select->where($this->column, 'in', new Parameter($values));
    }
}
