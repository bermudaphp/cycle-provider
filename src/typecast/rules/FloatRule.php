<?php

namespace api\typecast\rules;

use api\typecast\RuleInterface;

final class FloatRule implements RuleInterface
{
    /**
     * @param string $var
     */
    public function cast(mixed $var): float
    {
        return $var + 0;
    }

    /**
     * @param float $var
     */
    public function uncast(mixed $var): string
    {
        return "$var";
    }

    public function getName(): string
    {
        return 'float';
    }
}