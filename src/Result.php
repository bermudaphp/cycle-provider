<?php

namespace Bermuda\Cycle;

final class Result
{
    public function __construct(
        public readonly array $results,
        public readonly int $total
    ) {
    }
}
