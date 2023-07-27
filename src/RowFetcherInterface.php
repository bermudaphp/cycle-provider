<?php

namespace Bermuda\Cycle;

interface RowFetcherInterface
{
    public function fetch(array $row): array ;
}
