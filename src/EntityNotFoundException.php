<?php

namespace Bermuda\Cycle;

use Bermuda\HTTP\HttpException;

class EntityNotFoundException extends HttpException 
{
    public function __construct(
        public readonly string $role, 
        public readonly string $id
    ) {
        parent::__construct(sprintf('Entity: %s with id: %s not found!', $role, $id), 404);
    }
}
