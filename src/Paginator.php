<?php

namespace App;

use Bermuda\Utils\URL;

class Paginator
{
    private ?string $url = null;

    public function __construct(
        private array $results,
        private int $resultsCount,
        private array $queryParams = []
    ){
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = trim($url, '/?');
        return $this;
    }

    /**
     * @param array $results
     * @return $this
     */
    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @param array $queryParams
     * @return $this
     */
    public function setQueryParams(array $queryParams): self
    {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * @param int $resultsCount
     * @return $this
     */
    public function setResultsCount(int $resultsCount): self
    {
        $this->resultsCount = $resultsCount;
        return $this;
    }

    /**
     * @param array|null $mergeData
     * @return array
     */
    public function paginate(array $mergeData = null): array
    {
        $data = [
            'count'   => $this->resultsCount,
            'prev'    => $this->getPrevUrl(),
            'next'    => $this->getNextUrl(),
            'range'   => $this->getRange(),
            'results' => $this->results
        ];

        if ($mergeData != null) {
            return array_merge($mergeData, $data);
        }

        return $data;
    }

    /**
     * @return string|null
     */
    public function getNextUrl():? string
    {
        $queryParams = $this->queryParams;
        list($limit, $offset) = $this->parseQueryParams($queryParams);

        if ($this->resultsCount > ($offset = $limit + $offset)) {
            $queryParams['offset'] = $offset;
            return $this->buildUrl($queryParams);
        }

        return null;
    }

    public function getRange(): string
    {
        list(, $offset) = $this->parseQueryParams();

        if ($offset == 0) {
            return '1-'. count($this->results);
        }

        return $offset + 1 .'-'. $offset + count($this->results);
    }

    private function parseQueryParams(array $queryParams = null): array
    {
        if ($queryParams === null){
            $queryParams = $this->queryParams;
        }

        return [$queryParams['limit'] ?? 10, $queryParams['offset'] ?? 0];
    }

    /**
     * @return string|null
     */
    public function getPrevUrl():? string
    {
        $queryParams = $this->queryParams;

        list($limit, $offset) = $this->parseQueryParams($queryParams);

        if ($offset != 0) {
            if (($diff = $offset - $limit) > 0) {
                $queryParams['offset'] = $diff;
            } elseif ($diff == 0){
                unset($queryParams['offset']);
            }

            return $this->buildUrl($queryParams);
        }

        return null;
    }

    /**
     * @param array $queryParams
     * @return string
     */
    private function buildUrl(array $queryParams): string
    {
        if ($this->url === null) {
            return URL::build(['params' => $queryParams]);
        }

        return $this->url . ($queryParams != [] ? '?' . http_build_query($queryParams) : '');
    }
}
