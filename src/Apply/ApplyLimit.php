<?php

namespace App\Query\Applies;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyLimit
{
    public function __invoke(Select $select, SchemaInterface $schema, int $limit): Select
    {
        return $select->limit($limit);
    }
}