<?php

namespace App\Pages\Model\Book;

class NavigationElement
{
    public string $name = '';

    public ?string $page = null;

    /** @var NavigationElement[] */
    public array $children = [];

    public function __construct(string $name, ?string $page)
    {
        $this->name = $name;
        $this->page = $page;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'page' => $this->page,
            'children' => array_map(function (NavigationElement $child) {
                return $child->toArray();
            }, $this->children)
        ];
    }

    public static function fromArray(array $data): self
    {
        $self = new self($data['name'] ?? '', $data['page'] ?? null);
        $self->children = array_map(function ($child) {
            return self::fromArray($child);
        }, $data['children'] ?? []);

        return $self;
    }
}
