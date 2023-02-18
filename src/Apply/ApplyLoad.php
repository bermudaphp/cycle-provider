<?php

namespace Bermuda\Cycle\Apply;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyLoad
{
    public function __invoke(Select $select, SchemaInterface $schema, array $relations): Select
    {
        return $select->load($relations);
    }
}
