<?php

namespace App\Project\Model\Component;

use Ramsey\Uuid\Uuid;

class ComponentId
{
    private function __construct(
        private string $id
    ) {}

    public static function new(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
