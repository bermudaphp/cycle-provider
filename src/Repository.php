<?php

namespace Bermuda\Cycle;

use Bermuda\Paginator\QueryException;
use Bermuda\Paginator\Paginator;
use Bermuda\Paginator\QueryInterface;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;

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
