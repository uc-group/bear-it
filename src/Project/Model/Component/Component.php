<?php

namespace App\Project\Model\Component;

class Component
{
    public function __construct(
        private ComponentId $componentId,
        private string $name,
        private string $description,
        private string $icon
    ) {}

    public function id(): ComponentId
    {
        return $this->componentId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function icon(): string
    {
        return $this->icon;
    }
}
