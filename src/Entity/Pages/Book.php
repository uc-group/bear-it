<?php

namespace App\Entity\Pages;

use App\Entity\Embeddable\Resource;
use App\Entity\Project;
use App\Pages\Model\Book\BookId;
use App\Pages\Model\Book\NavigationElement;
use App\Project\Exception\InvalidProjectIdException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'bi_pages_book')]
#[ORM\UniqueConstraint(columns: ['resource_number', 'resource_project'])]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 80)]
    public string $name;

    #[ORM\Column(type: 'json', nullable: false)]
    public array $navigation = [];

    #[ORM\Embedded(class: Resource::class)]
    private Resource $resource;

    public function __construct(BookId $bookId, string $name)
    {
        $this->id = $bookId->toString();
        $this->resource = Resource::fromId($bookId);
        $this->name = $name;
    }

    /**
     * @throws InvalidProjectIdException
     */
    public function getId(): BookId
    {
        $id = $this->resource->id();
        return BookId::create($id->getProjectId(), $id->number());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->resource->id()->toString(),
            'name' => $this->name,
            'navigation' => empty($this->navigation) ? (new NavigationElement('root', null))->toArray() : $this->navigation
        ];
    }
}
