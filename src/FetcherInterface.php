<?php

namespace Bermuda\Cycle;

interface FetcherInterface
{
    public function fetch(array $row): array ;
}
