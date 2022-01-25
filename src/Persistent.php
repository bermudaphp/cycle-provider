<?php

namespace Bermuda\Cycle;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Select;
use Cycle\ORM\ORMInterface;

trait Persistent
{
    private ORMInterface $orm;
    private EntityManagerInterface $entityManager;

    public function __construct(Select $select, ORMInterface $orm)
    {
        $this->orm = $orm;
        parent::__construct($select);
        $this->entityManager = new EntityManager($orm);
    }

    /**
     * @param object $entity
     * @param bool $cascade
     * @return void
     * @throws \Throwable
     */
    private function persistEntity(object $entity, bool $cascade): void
    {
        $this->entityManager->persist($entity, $cascade)->run();
    }
}
