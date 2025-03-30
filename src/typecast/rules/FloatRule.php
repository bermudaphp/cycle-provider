<?php

namespace Bermuda\Cycle\Typecast\Rules;

use Bermuda\Cycle\Typecast\RuleInterface;

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
