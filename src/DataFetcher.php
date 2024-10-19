<?php

namespace Bermuda\Cycle;

use Bermuda\Cycle\Apply\ApplyLimit;
use Bermuda\Cycle\Apply\ApplyOffset;
use Bermuda\Cycle\Selectable;
use Cycle\Database\Injection\Fragment;
use Cycle\Database\Query\SelectQuery;
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
        array $applies = [
            'limit' => new ApplyLimit,
            'offset' => new ApplyOffset,
        ]
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

    protected function formQuery(?QueryInterface $query): SelectQuery
    {
        $select = $this->select($this->getSource());
        return $this->apply($query, $select);
    }

    protected function getSource(): SourceInterface
    {
        return $this->orm->getSource($this->getRole());
    }

    protected function getPrimaryKey(): string
    {
        $pk = $this->orm->getSchema()->define($this->getRole(), SchemaInterface::PRIMARY_KEY);

        if (is_array($pk)) $pk = "{$this->getSource()->getTable()}.$pk[0]";
        else $pk = "{$this->getSource()->getTable()}.$pk";

        return $pk;
    }

    protected function fetchResults(iterable $rows, int $count, ?QueryInterface $query): Result
    {
        $results = [];
        foreach ($rows as $row) $results[] = $this->getRowFetcher()->fetch($row);
        return new Result($results, $count);
    }

    protected function doFetch(?QueryInterface $query, bool $onlyCont = false):? Result
    {
        $select = $this->formQuery($query);
        $count = $this->countRows($select);
        if ($query->get('offset', 10) > $count) $count = 0;
        if ($onlyCont) return new Result([], $count);
        if ($count > 0) {
            return $this->fetchResults($select, $count, $query);
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

    protected function countRows(SelectQuery $select): int
    {
        return Counter::countDistinct($select, $this->getPrimaryKey());
    }

    protected function init(): void
    {
        $this->initColumns();
        $this->applies = $this->getApplies();
    }

    /**
     * @return Selectable[]
     */
    protected function getApplies(): array
    {
        return [];
    }

    abstract protected function getRole(): string ;

    protected function exceptedColumns(): array
    {
        return [];
    }
    
    protected function initColumns(): void
    {
        if ($this->columns == []) {
            $source = $this->orm->getSource($this->getRole());
            $columns = $this->orm->getSchema()->define($this->getRole(), SchemaInterface::COLUMNS);

            $excepted = $this->exceptedColumns();

            foreach ($columns as $alias => $column) {
                if (in_array($column, $excepted)) continue;
                if (is_int($alias)) $this->columns[] = "{$source->getTable()}.$column" ;
                else $this->columns[] = "{$source->getTable()}.$column as $alias" ;
            }
        }
    }
}
