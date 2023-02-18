<?php

namespace Bermuda\Cycle;

use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;

trait SelectableTrait
{
    public function apply(Select $select, SchemaInterface $schema): Select
    {
        $callbacks = $this->getApplyCallbacks();
        foreach ($this->queryParams as $name => $value) {
            if (isset($callbacks[$name])) $select = $callbacks[$name]($select, $schema, $value);
            else $select = $select->where($name, $this->queryParams[$name]);
        }

        return $this->finalize($select);
    }

    protected function finalize(Select $select): Select
    {
        return $select;
    }

    /**
     * $callback(Select $select, SchemaInterface $schema, mixed $value)
     * @return callable[]
     */
    protected function getApplyCallbacks(): array
    {
        return [];
    }
}
