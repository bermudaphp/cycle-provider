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

    /**
     * @param object $entity
     * @param bool $cascade
     * @return void
     * @throws \Throwable
     */
    protected function persistEntity(object $entity, bool $cascade): void
    {
        $this->entityManager->persist($entity, $cascade)->run();
    }

    /**
     * @param QueryInterface $query
     * @return Paginator
     * @throws QueryException
     */
    public function paginate(QueryInterface $query): Paginator
    {
        $select = $this->buildQuery($query);
        $resultsCount = $query->has('offset') ? (clone $select)->offset(0)->count() :
            (clone $select)->count();

        if ($resultsCount === 0) {
            return Paginator::createEmpty($query);
        }

        $results = [];
        foreach ($select as $entity) $results[] = $entity->toArray();

        return new Paginator($results, $resultsCount ?? 100, $query);
    }


    /**
     * @throws QueryException
     */
    protected function buildQuery(array|QueryInterface $query): Select
    {
        if ($query == []) return $this->select();

        $select = $this->select();
        if ($query instanceof Selectable) {
            return $query->apply($select, $this->orm->getSchema());
        }

        return $this->getQueryInstance($query)->apply($select, $this->orm->getSchema());
    }

    abstract protected function getQueryInstance(QueryInterface $query): Selectable ;
}
