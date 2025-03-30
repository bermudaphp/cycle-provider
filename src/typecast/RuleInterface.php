<?php

namespace Bermuda\Cycle\Typecast;

interface RuleInterface
{
    public function cast(mixed $var): mixed;
    public function uncast(mixed $var): mixed ;

    public function getName(): string;
}
