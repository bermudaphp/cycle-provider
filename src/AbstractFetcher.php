<?php

namespace Bermuda\Cycle;

use Bermuda\Cycle\Apply\ApplyLimit;
use Bermuda\Cycle\Apply\ApplyOffset;
use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;

abstract class AbstractFetcher implements OrmAwareInterface
{
    /**
     * @var Selectable[]
     */
    protected array $applies = [];

    protected array $columns = [];
    
    private ?RowFetcher $rowFetcher = null;

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

    protected function doFetch(?QueryInterface $query):? Result
    {
        $pk = $this->orm->getSchema()->define($this->getRole(), SchemaInterface::PRIMARY_KEY);
        if (is_array($pk)) $pk = $pk[0];

        $source = $this->orm->getSource($this->getRole());
        $select  = $source->getDatabase()
            ->select($this->columns)
            ->from($source->getTable());

        if ($query !== null) {
            foreach ($query->toArray() as $name => $value) {
                if (isset($this->applies[$name])) $select = $this->applies[$name]->apply($select, $value);
            }
        }
        
        $total = (clone $select)->offset()
            ->columns(new Fragment("count(distinct {$source->getTable()}.$pk) as total"))
            ->run()->fetch()['total'];

        if ($total > 0) {
            $results = [];
            foreach ($select as $row) $results[] = $this->fetchRow($row);
            return new Result($results, $total);
        }

        return null;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function fetchRow(array $row): array
    {
        if (!$this->rowFetcher) $this->rowFetcher = new RowFetcher($this->getRole(), $this->orm);
        return $this->rowFetcher->fetch($row);
    }

    protected function init(): void
    {
        $this->initColumns();
        $this->add('limit', new ApplyLimit);
        $this->add('offset', new ApplyOffset);
    }

    abstract protected function getRole(): string ;
    
    protected function initColumns(): void
    {
        if ($this->columns == []) {
            $source = $this->orm->getSource($this->getRole());
            $columns = $this->orm->getSchema()->define($this->getRole(), SchemaInterface::COLUMNS);

            foreach ($columns as $alias => $column) {
                if (is_int($alias)) $this->columns[] = "{$source->getTable()}.$column" ;
                else $this->columns[] = "{$source->getTable()}.$column as $alias" ;
            }
        }
    }
}
