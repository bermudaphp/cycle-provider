<?php

namespace Bermuda\Cycle;

use Bermuda\StdLib\Arrayable;

interface QueryInterface extends Arrayable
{
    public function get(string $name, mixed $default = null);
}
