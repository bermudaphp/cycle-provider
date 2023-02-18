<?php

namespace App\Query\Applies;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

final class ApplyOrder
{
    public const asc = 'asc';
    public const desc = 'desc';

    public readonly string $mode;

    public function __construct(
        public readonly string $column,
        string $mode = self::asc
    ) {
        $this->mode = strtolower($mode) === self::asc
            ? self::asc : self::desc;
    }

    public function __invoke(Select $select, SchemaInterface $schema, int $offset): Select
    {
        return $select->orderBy($this->column, $this->mode);
    }
}