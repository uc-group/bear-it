<?php

namespace BearIt\User\Model;

class UserId
{
    private $id;

    /**
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $id
     * @return self
     */
    public static function fromString(string $id): self
    {
        return new self($id);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->id;
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

        return $this->id === $other->id;
    }
}
