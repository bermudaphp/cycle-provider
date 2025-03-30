<?php

namespace api\typecast;

use api\typecast\rules\ArrayRule;
use api\typecast\rules\BooleanRule;
use api\typecast\rules\CarbonRule;
use api\typecast\rules\FloatRule;
use api\typecast\rules\IntRule;
use api\typecast\rules\UuidRule;

final class RuleCollector
{
    private array $rules = [];

    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->addRules($rules);
    }

    public function addRule(RuleInterface $rule): void
    {
        $this->rules[$rule->getName()] = $rule;
    }

    /**
     * @param iterable<RuleInterface> $rules
     */
    public function addRules(iterable $rules): void
    {
        foreach ($rules as $rule) $this->addRule($rule);
    }

    public function hasRule(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    public function getRule(string $name): ?RuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    public function cast(string $name, mixed $var): mixed
    {
        return $this->getRule($name)?->cast($var) ?? $var;
    }

    public function uncast(string $name, mixed $var): mixed
    {
        return $this->getRule($name)?->uncast($var) ?? $var;
    }

    public function getNames(): array
    {
        return array_keys($this->rules);
    }

    public static function fromDefaults(): self
    {
        return new self([
            new ArrayRule,
            new BooleanRule,
            new CarbonRule,
            new FloatRule,
            new IntRule,
            new UuidRule,
        ]);
    }
}