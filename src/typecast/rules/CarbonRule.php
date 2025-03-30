<?php

namespace api\typecast\rules;

use Bermuda\Clock\Clock;
use Carbon\CarbonInterface;
use api\typecast\RuleInterface;

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