<?php

namespace Bermuda\Cycle;

use Bermuda\Cycle\Apply\ApplyLimit;
use Bermuda\Cycle\Apply\ApplyOffset;
use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Query\SelectQuery;
use SelectQuery as Counter;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\SourceInterface;

abstract class DataFetcher implements OrmAwareInterface
{
    /**
     * @var Selectable[]
     */
    protected array $applies = [];

    protected array $columns = [];
    
    protected ?RowFetcher $rowFetcher = null;

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

    protected function select(SourceInterface $source): SelectQuery
    {
        return $source->getDatabase()
            ->select($this->columns)
            ->from($source->getTable());
    }

    protected function doFetch(?QueryInterface $query):? Result
    {
        $source = $this->orm->getSource($this->getRole());

        $pk = $this->orm->getSchema()->define($this->getRole(), SchemaInterface::PRIMARY_KEY);
        if (is_array($pk)) $pk = "{$source->getTable()}.$pk[0]";
        else $pk = "{$source->getTable()}.$pk";

        $select = $this->select($source);
        $select = $this->apply($query, $select);

        if (($count = $this->countRows($select, $pk)) > 0) {
            $results = [];
            foreach ($select as $row) $results[] = $this->getRowFetcher()->fetch($row);
            return new Result($results, $count);
        }

        return null;
    }

    protected function getRowFetcher(): RowFetcher
    {
        if (!$this->rowFetcher) $this->rowFetcher = new RowFetcher($this->getRole(), $this->orm);
        return $this->rowFetcher;
    }

    protected function apply(?QueryInterface $query, SelectQuery $select): SelectQuery
    {
        if (!$query) return $select;
        foreach ($query->toArray() as $name => $value) {
            if (isset($this->applies[$name])) {
                $select = $this->applies[$name]->apply($select, $value);
                if ($this->applies[$name] instanceof RowFetcherInterface) {
                    $this->getRowFetcher()->extend($this->applies[$name]);
                }
            }
        }

        return $select;
    }

    protected function countRows(SelectQuery $select, string $primaryKey): int
    {
        return Counter::countDistinct($select, $primaryKey);
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
