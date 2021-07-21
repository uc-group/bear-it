<?php

namespace App\Task\Model\Task;

use App\Exception\InvalidIdFormatException;

class Status
{
    const NEW = 'new';
    const ACKNOWLEDGED = 'acknowledged';
    const IN_PROGRESS = 'in_progress';
    const IN_REVIEW = 'in_review';
    const POSTPONED = 'postponed';
    const DONE = 'done';
    const CLOSED = 'closed';

    const VALID_STATUSES = [
        self::NEW,
        self::ACKNOWLEDGED,
        self::IN_PROGRESS,
        self::IN_REVIEW,
        self::POSTPONED,
        self::DONE,
        self::CLOSED,
    ];

    private function __construct(
        private string $status
    ) {}

    public static function fromString(string $status)
    {
        if (!in_array($status, self::VALID_STATUSES)) {
            new InvalidIdFormatException($status);
        }

        return new self($status);
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public static function acknowledged(): self
    {
        return new self(self::ACKNOWLEDGED);
    }

    public static function inProgress(): self
    {
        return new self(self::IN_PROGRESS);
    }

    public static function inReview(): self
    {
        return new self(self::IN_REVIEW);
    }

    public static function postponed(): self
    {
        return new self(self::POSTPONED);
    }

    public static function done(): self
    {
        return new self(self::DONE);
    }

    public static function closed(): self
    {
        return new self(self::CLOSED);
    }

    public function toString(): string
    {
        return $this->status;
    }

    public function equals($other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->status === $other->status;
    }
}
