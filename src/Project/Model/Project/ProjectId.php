<?php

namespace App\Project\Model\Project;

use App\Utils\Id;

class ProjectId
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
     * @return ProjectId
     */
    public static function new(): self
    {
        return new self(Id::generateUuid());
    }

    /**
     * @param string $id
     * @return ProjectId
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