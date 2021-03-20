<?php

namespace Bermuda\Cycle;

/**
 * Class Paginator
 * @package Bermuda\Cycle
 */
class Paginator
{
    private string $url;
    private array $results;
    private array $queryParams;
    private int $limit = 10;
    private int $offset = 0;
    private int $resultsCount;

    public function __construct(string $url, array $results, int $resultsCount, array $queryParams = [])
    {
        $this->url = $url;
        $this->results = $results;
        $this->resultsCount = $resultsCount;
        $this->queryParams = $queryParams;
    }

    /**
     * @param array|null $queryParams
     * @return array
     */
    public function queryParams(array $queryParams = null): array
    {
        if ($queryParams != null)
        {
            $this->queryParams = $queryParams;
        }
        
        return $this->queryParams;
    }

    /**
     * @return array
     */
    public function paginate():array
    {
        return [
            'count'   => $this->resultsCount,
            'prev'    => $this->getPrevUrl(),
            'next'    => $this->getNextUrl(),
            'results' => $this->results,
        ];
    }

    /**
     * @param int|null $limit
     * @return int
     */
    public function limit(?int $limit = null): int
    {
        if ($limit != null)
        {
            $this->limit = $limit;
        }

        return $this->limit;
    }

    /**
     * @param int|null $offset
     * @return int
     */
    public function offset(?int $offset = null): int
    {
        if ($offset != null)
        {
            $this->offset = $offset;
        }

        return $this->offset;
    }

    /**
     * @return string|null
     */
    public function getNextUrl():? string
    {
        $queryParams = $this->queryParams;

        if ($this->resultsCount > ($sum = $this->limit + $this->offset))
        {
            $queryParams[$this->offsetParam()] = $sum;

            if ($this->limit != 10)
            {
                $queryParams[$this->limitParam()] = $this->limit;
            }

            return $this->buildUrl($queryParams);
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getPrevUrl():? string
    {
        $queryParams = [];

        if ($this->offset != 0)
        {
            if (($diff = $this->offset - $this->limit) >= 0)
            {
                if ($diff > 0)
                {
                    $queryParams[$this->offsetParam()] = $diff;
                }

                if ($this->limit != 10)
                {
                    $queryParams[$this->limitParam()] = $this->limit;
                }

                return $this->buildUrl($queryParams);
            }

            return $this->buildUrl($queryParams);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getResultsCount(): int
    {
        return $this->resultsCount;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param array $queryParams
     * @return string
     */
    private function buildUrl(array $queryParams): string
    {
        return $this->url . ($queryParams != [] ? '?' . http_build_query($queryParams) : '');
    }

    /**
     * @return string
     */
    protected function limitParam(): string
    {
        return 'limit';
    }

    /**
     * @return string
     */
    protected function offsetParam(): string
    {
        return 'offset';
    }
}
