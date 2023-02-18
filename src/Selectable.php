<?php

namespace Bermuda\Cycle\Query;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

interface Selectable
{
    /**
     * @param Select $select
     * @param SchemaInterface $schema
     * @return Select
     * @throws QueryException
     */
    public function apply(Select $select, SchemaInterface $schema): Select ;
}
