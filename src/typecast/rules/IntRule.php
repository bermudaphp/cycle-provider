<?php

namespace api\typecast\rules;

use api\typecast\RuleInterface;

final class IntRule implements RuleInterface
{
    /**
     * @param string $var
     * @return int
     */
    public function cast(mixed $var): int
    {
        return $var + 0;
    }

    /**
     * @param int $var
     * @return string
     */
    public function uncast(mixed $var): string
    {
        return "$var";
    }

    public function getName(): string
    {
        return 'int';
    }
}