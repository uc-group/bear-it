<?php

namespace App\Pages\Model;

class PageEdit
{
    public function __construct(
        private string $id,
        private string $name,
        private string $content
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
