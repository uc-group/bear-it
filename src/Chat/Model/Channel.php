<?php

namespace App\Chat\Model;

class Channel
{
    public function __construct(
        private string $room,
        private string $name
    ) {}

    public function room(): string
    {
        return $this->room;
    }

    public function name(): string
    {
        return $this->name;
    }
}
