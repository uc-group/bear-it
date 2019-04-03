<?php

namespace App\ValueObject\Id;

use App\Exception\InvalidIdFormatException;

class Task
{
    const PREFIX_INVALID = 'INVALID';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int
     */
    private $number;

    /**
     * @param string $prefix
     * @param int $number
     */
    private function __construct(string $prefix, int $number)
    {
        $this->prefix = $prefix;
        $this->number = $number;
    }

    /**
     * @param string $id
     * @return Task
     * @throws InvalidIdFormatException
     */
    public static function fromString(string $id): self
    {
        if (!preg_match('/^(.*)-(\d+)$/', $id, $m)) {
            throw new InvalidIdFormatException($id);
        }

        return new self($m[1], (int)$m[2]);
    }

    /**
     * @param string $prefix
     * @param int $number
     * @return Task
     */
    public static function create(string $prefix, int $number): self
    {
        return new self($prefix, $number);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return sprintf('%s-%d', $this->prefix, $this->number);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}