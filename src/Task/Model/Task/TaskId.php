<?php

namespace App\Task\Model\Task;

use App\Exception\InvalidIdFormatException;

class TaskId
{
    const PREFIX_INVALID = 'INVALID';

    private string $prefix;

    private int $number;

    private function __construct(string $prefix, int $number)
    {
        $this->prefix = $prefix;
        $this->number = $number;
    }

    /** @throws InvalidIdFormatException */
    public static function fromString(string $id): self
    {
        if (!preg_match('/^(.*)-(\d+)$/', $id, $m)) {
            throw new InvalidIdFormatException($id);
        }

        return new self($m[1], (int)$m[2]);
    }

    public static function create(string $prefix, int $number): self
    {
        return new self($prefix, $number);
    }

    public function toString(): string
    {
        return sprintf('%s-%d', $this->prefix, $this->number);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}