<?php

namespace Bermuda\Cycle\Apply;

use Cycle\Database\Injection\Fragment;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyDateTime
{
    public function __construct(
        public readonly string $column,
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    public function __invoke(Select $select, SchemaInterface $schema, \DateTimeInterface $date): Select
    {
        return $select->where(new Fragment("Date($this->column)"), $date->format($this->dateTimeFormat));
    }
}
