<?php

namespace BearIt\Access\Model\AccessFunction;

use BearIt\Core\ValueObject\ValueObjectInterface;

class AccessFunction implements ValueObjectInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * AccessFunction constructor.
     * @param string $name
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return AccessFunction
     */
    public static function fromString(string $name): self
    {
        return new self($name);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @param $other
     * @return bool
     */
    public function equals($other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->name === $other->name;
    }
}
