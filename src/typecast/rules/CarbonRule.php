<?php

namespace Bermuda\Cycle\Typecast\Rules;

use Bermuda\Clock\Clock;
use Carbon\CarbonInterface;
use Bermuda\Cycle\Typecast\RuleInterface;

final class CarbonRule implements RuleInterface
{
    /**
     * @param string $var
     * @return CarbonInterface
     */
    public function cast(mixed $var): CarbonInterface
    {
        return Clock::create($var);
    }

    /**
     * @param CarbonInterface $var
     */
    public function uncast(mixed $var): string
    {
       return (string) $var;
    }

    public function getName(): string
    {
        return 'carbon';
    }
}
