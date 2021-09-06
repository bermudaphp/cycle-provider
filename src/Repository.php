<?php

namespace Bermuda\Cycle;

use App\Entity\Post;
use Bermuda\Arrayable;
use Bermuda\Utils\Url;
use Cycle\ORM\Select;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Cycle\ORM\TransactionInterface;
use Psr\Container\ContainerInterface;
use Spiral\Database\DatabaseInterface;
use \Cycle\ORM\Select\Repository as CycleRepository;
use Spiral\Database\Injection\Parameter;
use function Bermuda\route;

/**
 * Class Repository
 * @package Bermuda\Cycle
 */
abstract class Repository extends CycleRepository implements RepositoryInterface
{
    protected ORMInterface $orm;
    protected ?TransactionInterface $transaction = null;

    protected string $routeName = '';

    public function __construct(Select $select, ORMInterface $orm)
    {
        $this->orm = $orm;
        parent::__construct($select);
    }

    /**
     * @return DatabaseInterface
     */
    protected function getDb(): DatabaseInterface
    {
        return $this->orm->getSource($this->getRole())->getDatabase();
    }

    /**
     * @param array $identity
     * @return bool
     */
    public function exists(array $identity): bool
    {
        return $this->select()->where($identity)->count() > 0;
    }

    /**
     * @param object $entity
     * @param int $mode
     * @throws \Throwable
     */
    protected function storeEntity(object $entity, int $mode = TransactionInterface::MODE_CASCADE): void
    {
        $this->getTransaction()->persist($entity, $mode)->run();
    }

    /**
     * @param array $payload
     * @return object
     */
    protected function makeEntity(array $payload): object
    {
        return $this->orm->make($this->getRole(), $payload);
    }

    /**
     * @return string
     */
    abstract protected static function getRole(): string ;

    /**
     * @return Transaction
     */
    protected function getTransaction(): Transaction
    {
        if (!$this->transaction)
        {
            $this->transaction = new Transaction($this->orm);
        }

        return $this->transaction;
    }

    /**
     * @param array $datum
     * @return array
     */
    protected function fetchData(array $datum): array
    {
        $result = [];

        foreach ($datum as $name => $value)
        {
            if (in_array($name, $this->getExceptedFields()))
            {
                continue;
            }

            if (!is_null($value))
            {
                if (is_array($value))
                {
                    $value = $this->fetchRelation($name, $value);

                    if ($value == [])
                    {
                        continue;
                    }
                }

                elseif ($value instanceof Arrayable)
                {
                    $value = $value->toArray();
                }

                elseif ($value instanceof \Stringable)
                {
                    $value = $value->__toString();
                }

                $result[$name] = $value;
            }
        }

        return $result;
    }

    protected function getExceptedFields(): array
    {
        return [];
    }

    /**
     * @param string $name
     * @param array $value
     * @return array
     */
    protected function fetchRelation(string $name, array $value): array
    {
        if (method_exists($this, $method = 'fetch' . $name))
        {
            return $this->{$method}($value);
        }

        $items = [];

        foreach ($value as $item)
        {
            if (isset($item['@']))
            {
                $items[] = array_filter($item['@'], static function ($v)
                {
                    return $v != null;
                });

                continue;
            }

            $items = $value;
            break;
        }

        return $items;
    }

    /**
     * @param array $queryParams
     * @return array[]
     */
    public function get(array $queryParams = []): array
    {
        $select = $this->buildSelectQuery($queryParams);
        $paginator = $this->getPaginator($select, $queryParams);

        return $paginator->paginate();
    }

    /**
     * @param $id
     * @return object
     * @throws \RuntimeException
     */
    public function findById($id): object
    {
        $object = $this->findByPK($id);

        if ($object == null)
        {
            throw new \RuntimeException(
                sprintf('Entity: %s with id: %s not found!', $this->getRole(), $id), 404
            );
        }

        return $object;
    }

    public function destroy($objectOrId, bool $cascade = true): void
    {
        if (is_string($objectOrId))
        {
            $objectOrId = [$objectOrId];
        }

        elseif (is_array($objectOrId))
        {
            $objectOrId = $this->select()->where($this->loader->getPK(), 'in', new Parameter($objectOrId))
                ->fetchAll();

            foreach ($objectOrId as $obj)
            {
                $this->getTransaction()->delete($objectOrId, $cascade
                    ? Transaction::MODE_CASCADE
                    : Transaction::MODE_ENTITY_ONLY
                );
            }

            $this->getTransaction()->run();
        }

        elseif ($role = $this->getRole() && $objectOrId instanceof $role)
        {
            $this->getTransaction()->delete($objectOrId, $cascade
                ? Transaction::MODE_CASCADE
                : Transaction::MODE_ENTITY_ONLY
            )->run();
        }

        else
        {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * @inheritDoc
     */
    public function store(object $object, bool $cascade = true): object
    {
        $this->getTransaction()->persist($object,
            $cascade ? Transaction::MODE_CASCADE
                : Transaction::MODE_ENTITY_ONLY
        );

        $this->getTransaction()->run();

        return $object;
    }

    /**
     * @param array $queryParams
     * @return int
     */
    public function count(array $queryParams): int
    {
        return $this->buildSelectQuery($queryParams)->count();
    }

    protected function getPaginator(Select $select, array $queryParams): Paginator
    {
        $count = $select->count();
        list($limit, $offset) = $this->parseQueryParams($queryParams);

        $paginator = new Paginator($this->getBaseUrl(),
            $this->fetchAll($select->limit($limit)
                ->offset($offset), $queryParams),
            $count, $queryParams
        );

        $paginator->limit($limit);
        $paginator->offset($offset);

        return $paginator;
    }

    protected function parseQueryParams(array $queryParams): array
    {
        if (isset($queryParams['limit']) && ($limit = (int) $queryParams['limit']) < 100)
        {
            $queryParams['limit'] = $limit;
        }

        return [$queryParams['limit'] ?? 10, (int) ($queryParams['offset'] ?? 0)];
    }

    /**
     * @param Select $select
     * @return array
     */
    protected function fetchAll(Select $select, array $queryParams): array
    {
        $results = [];

        foreach ($select->fetchData() as $datum)
        {
            $results[] = $this->fetchData($datum);
        };

        return $results;
    }

    /**
     * @param array $queryParams
     * @return Select
     */
    protected function buildSelectQuery(array $queryParams): Select
    {
        $select = $this->select();

        if (isset($queryParams['load']))
        {
            foreach ($this->parse($queryParams['load']) as $r)
            {
                $select->load($r);
            }
        }

        return $select;
    }

    /**
     * @param string $subject
     * @param int $limit
     * @return array
     */
    protected function parse(string $subject, int $limit = PHP_INT_MAX): array
    {
        return explode(',', $subject, $limit);
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        if ($this->routeName == '')
        {
            throw new \RuntimeException('Overwrite self::$routeName');
        }

        return route($this->routeName, asUrl: true);
    }

    /**
     * @param ContainerInterface $container
     * @return static
     */
    public static function newSelf(ContainerInterface $container): self
    {
        return $container->get(ORMInterface::class)->getRepository(static::getRole());
    }
}
