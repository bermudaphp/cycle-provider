<?php

namespace Bermuda\Cycle\Exceptions;

class EntityNotFound extends \RuntimeException 
{
    public function __construct(private string $role, private string $id)
    {
        parent::__construct(sprintf('Entity: %s with id: %s not found!', $role, $id), 404);
    }
  
    public function getRole(): string
    {
        return $this->role;
    }
  
    public function getId(): string
    {
        return $this->id;
    }
}
