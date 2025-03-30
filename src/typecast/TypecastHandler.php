<?php

namespace Bermuda\Cycle\Typecast;

use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;

final class TypecastHandler implements CastableInterface, UncastableInterface
{
    private array $rules = [];
    public readonly RuleCollector $collector;

    /**
     * @param iterable<RuleInterface> $rules
     */
    public function __construct(iterable $rules = [])
    {
        $this->collector = RuleCollector::fromDefaults();
        if ($rules) $this->collector->addRules($rules);
    }

    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if ($this->collector->hasRule($rule)) {
                unset($rules[$key]);
                $this->rules[$key] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @inerhitDoc
     */
    public function cast(array $data): array
    {
        foreach ($this->rules as $column => $rule) {
            if (isset($data[$column])) $data[$column] = $this->collector->cast($rule, $data[$column]);
        }

        return $data;
    }

    /**
     * @inerhitDoc
     */
    public function uncast(array $data): array
    {
        foreach ($this->rules as $column => $rule) {
            if (isset($data[$column])) $data[$column] = $this->collector->uncast($rule, $data[$column]);
        }

        return $data;
    }
}
