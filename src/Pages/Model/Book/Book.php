<?php

namespace App\Pages\Model\Book;

class Book
{
    private BookId $id;
    public string $name;
    private NavigationElement $navigation;

    private function __construct(BookId $bookId, string $name)
    {
        $this->id = $bookId;
        $this->name = $name;
        $this->navigation = new NavigationElement('root', null);
    }

    public static function create(BookId $bookId, string $name): self
    {
        return new self($bookId, $name);
    }

    public function id(): BookId
    {
        return $this->id;
    }

    public function navigation(): NavigationElement
    {
        return $this->navigation;
    }

    public function updateNavigation(NavigationElement $navigationElement) {
        $this->navigation = $navigationElement;
    }

    public static function fromArray(array $data): self
    {
        $self = new self(BookId::fromString($data['id']),  $data['name']);
        $self->navigation = NavigationElement::fromArray($data['navigation']);

        return $self;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'navigation' => $this->navigation
        ];
    }
}
