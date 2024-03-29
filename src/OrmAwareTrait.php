<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;

trait OrmAwareTrait
{
    protected ?ORMInterface $orm = null;
    
    public function setOrm(ORMInterface $orm): OrmAwareInterface
    {
        $this->orm = $orm;
        return $this;
    }
}
