<?php

namespace Bermuda\Cycle;

use Bermuda\Cycle\Apply\ApplyLimit;
use Bermuda\Cycle\Apply\ApplyOffset;
use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\ORM\ORMInterface;

abstract class AbstractFetcher implements OrmAwareInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var Selectable[]
     */
    protected array $applies = [];

    /**
     * @param ORMInterface $orm
     * @param Selectable[] $applies
     */
    public function __construct(
        protected ORMInterface $orm,
        array $applies = []
    ) {
        $this->init();
        foreach ($applies as $name => $apply) $this->add($name, $apply);
    }

    /**
     * @param string $name
     * @param Selectable $apply
     * @return void
     */
    public function add(string $name, Selectable $apply): void
    {
        if ($apply instanceof OrmAwareInterface) $apply->setOrm($this->orm);
        $this->applies[$name] = $apply;
    }

    public function setOrm(ORMInterface $orm): OrmAwareInterface
    {
        $this->orm = $orm;
        return $this;
    }

    public function setFetchCallback(callable $callback): void
    {
        $this->callback = static fn(array $row): array => $callback($row);
    }

    protected function doFetch(QueryInterface $query, string $role, string $pk = 'id'):? Result
    {
        $source = $this->orm->getSource($role);
        $select  = $source->getDatabase()
            ->select("{$source->getTable()}.*")
            ->from($source->getTable());

        foreach ($query->toArray() as $name => $value) {
            if (isset($this->applies[$name])) {
                $select = $this->applies[$name]->apply($select, $value);
            }
        }

        $total = (clone $select)->offset()
            ->columns(new Fragment("count(distinct {$source->getTable()}.$pk) as total"))
            ->run()->fetch()['total'];

        if ($total > 0) {
            $results = [];
            foreach ($select as $row) $results[] = ($this->callback)($row);
            return new Result($results, $total);
        }

        return null;
    }

    protected function init(): void
    {
        $this->add('limit', new ApplyLimit);
        $this->add('offset', new ApplyOffset);
        $this->callback = static function(array $row): array  {
            return (new ArrayWrapper($row))->transform(new Transformer)->toArray();
        };
    }
}
