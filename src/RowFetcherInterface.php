<?php

namespace Bermuda\Cycle;

interface RowFetcherInterface
{
    public function fetch(array $row): array ;
    public function extend(RowFetcherInterface $fetcher): RowFetcherInterface ;
}
