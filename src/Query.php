<?php

namespace Bermuda\Cycle;

class Query implements QueryInterface
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->data[$name] ?? $default;
    }
}
