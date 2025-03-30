<?php

namespace api\typecast\rules;

use api\typecast\RuleInterface;

final class BooleanRule implements RuleInterface
{
    /**
     * @param int $var
     */
    public function cast(mixed $var): bool
    {
        return $var > 0;
    }

    /**
     * @param bool $var
     * @return string
     */
    public function uncast(mixed $var): int
    {
        return $var ? 1 : 0 ;
    }

    public function getName(): string
    {
        return 'boolean';
    }
}