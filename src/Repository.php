<?php

namespace Bermuda\Cycle;

use Cycle\ORM\Select;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;

abstract class Repository extends Select\Repository
{
    protected ORMInterface $orm;
    protected EntityManagerInterface $entityManager;

    public function __construct(Select $select, ORMInterface $orm)
    {
        $this->orm = $orm;
        parent::__construct($select);
        $this->entityManager = new EntityManager($orm);
    }
}
