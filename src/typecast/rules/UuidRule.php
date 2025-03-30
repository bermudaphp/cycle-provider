<?php

namespace api\typecast\rules;

use api\typecast\RuleInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

final class UuidRule implements RuleInterface
{
    private UuidFactory $factory;

    public function __construct(
       ?UuidFactory $factory = null
    ) {
        $this->factory = $factory ?? new UuidFactory();
    }

    /**
     * @param string $var
     */
    public function cast(mixed $var): UuidInterface
    {
        return $this->factory->fromBytes($var);
    }

    /**
     * @param UuidInterface $var
     */
    public function uncast(mixed $var): string
    {
        return $var->getBytes();
    }

    public function getName(): string
    {
        return 'uuid';
    }
}