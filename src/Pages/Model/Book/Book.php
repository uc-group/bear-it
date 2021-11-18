<?php

namespace App\Pages\Model\Book;

class Book
{
    private BookId $id;
    public string $name;
    private array $navigation = [];

    private function __construct(BookId $bookId, string $name)
    {
        $this->id = $bookId;
        $this->name = $name;
    }

    public static function create(BookId $bookId, string $name): self
    {
        return new self($bookId, $name);
    }

    public function id(): BookId
    {
        return $this->id;
    }

    public function navigation(): array
    {
        return $this->navigation;
    }

    public function fromArray(array $data): self
    {
        $this->id = BookId::fromString($data['id']);
        $this->name = $data['name'];
        $this->navigation = $data['navigation'];
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
