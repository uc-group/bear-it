<?php

namespace App\Pages\Model\Book;

class NavigationElement
{
    public string $name = '';

    public ?string $page = null;

    /** @var NavigationElement[] */
    public array $children = [];

    public function toArray(): array {
        return [
            'name' => $this->name,
            'page' => $this->page,
            'children' => array_map(function (NavigationElement $child) {
                return $child->toArray();
            }, $this->children)
        ];
    }
}
