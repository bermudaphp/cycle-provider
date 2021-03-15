<?php

namespace Bermuda\Cycle;

/**
 * Interface RepositoryInterface
 * @package Bermuda\Cycle\Repository
 */
interface RepositoryInterface
{
    /**
     * @param array $identity
     * @return bool
     */
    public function exists(array $identity): bool ;

    /**
     * @param array $queryParams
     * @return array[]
     */
    public function get(array $queryParams = []): array ;

    /**
     * @param array $queryParams
     * @return int
     */
    public function count(array $queryParams): int ;
}
