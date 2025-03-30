<?php

namespace Bermuda\Cycle\Typecast\Rules;

use Bermuda\Cycle\Typecast\RuleInterface;

final class ArrayRule implements RuleInterface
{
    /**
     * @param string $var
     * @return array
     * @throws \JsonException
     */
    public function cast(mixed $var): array
    {
        return json_decode($var, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @param array $var
     * @return string
     * @throws \JsonException
     */
    public function uncast(mixed $var): string
    {
        return json_encode($var, JSON_THROW_ON_ERROR);
    }

    public function getName(): string
    {
        return 'array';
    }
}
