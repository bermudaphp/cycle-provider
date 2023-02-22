<?php

namespace Bermuda\Cycle;

use Cycle\ORM\ORMInterface;

interface OrmAwareInterface
{
    public function setOrm(ORMInterface $orm): OrmAwareInterface ;
}
