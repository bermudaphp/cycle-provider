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
        return array_merge($this->data, get_object_vars($this));
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->data[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }
}
