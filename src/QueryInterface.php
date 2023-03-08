<?php

namespace Bermuda\Cycle;

use Bermuda\Stdlib\Arrayable;

interface QueryInterface extends Arrayable
{
    public function get(string $name, mixed $default = null);
}
