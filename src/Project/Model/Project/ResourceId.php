<?php

namespace App\Project\Model\Project;

use App\Exception\InvalidIdFormatException;

class ResourceId
{
    private string $prefix;
    private int $number;

    private function __construct(string $prefix, int $number)
    {
        $this->prefix = $prefix;
        $this->number = $number;
    }

    /** @throws InvalidIdFormatException */
    public static function fromString(string $id): static
    {
        if (!preg_match('/^(.*)-(\d+)$/', $id, $m)) {
            throw new InvalidIdFormatException($id);
        }

        return new static($m[1], (int)$m[2]);
    }

    public static function create(ProjectId $projectId, int $number): static
    {
        return new static($projectId->toString(), $number);
    }

    public function toString(): string
    {
        return sprintf('%s-%d', $this->prefix, $this->number);
    }

    public function getProjectId(): ProjectId
    {
        return ProjectId::fromString($this->prefix);
    }

    public function number(): int
    {
        return $this->number;
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

        return $this->prefix === $other->prefix && $this->number === $other->number;
    }
}
