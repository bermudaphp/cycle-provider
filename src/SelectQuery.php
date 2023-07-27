<?php

namespace Bermuda\Cycle;

use Cycle\Database\Injection\Fragment;

final class SelectQuery extends \Cycle\Database\Query\SelectQuery
{
    public static function countDistinct(\Cycle\Database\Query\SelectQuery $select, string $column): int
    {
        $select = clone $select;

        $select->columns = [new Fragment('count(distinct '.self::wrapColumn($column).')')];
        $select->orderBy = [];
        $select->groupBy = [];

        $select->limit(null);
        $select->offset(null);

        $st = $select->run();
        try {
            return (int) $st->fetchColumn();
        } finally {
            $st->close();
        }
    }

    private static function wrapColumn(string $column): string
    {
        if (str_contains($column, '.')) {
            return implode('.',
                array_map(static fn($segment) => "`$segment`",
                    explode('.', $column)
                )
            );
        }

        return "`$column`";
    }
}
