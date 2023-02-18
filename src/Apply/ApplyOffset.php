<?php

namespace App\Query\Applies;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyOffset
{
    public function __invoke(Select $select, SchemaInterface $schema, int $offset): Select
    {
        return $select->offset($offset);
    }
}