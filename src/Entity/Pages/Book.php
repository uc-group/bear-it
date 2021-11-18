<?php

namespace App\Entity\Pages;

use App\Entity\Project;
use App\Pages\Model\Book\BookId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'bi_pages_book')]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 80)]
    public string $name;

    #[ORM\Column(type: 'json', nullable: false)]
    public array $navigation = [];

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private Project $project;

    public function __construct(Project $project, BookId $bookId, string $name)
    {
        $this->id = $bookId->toString();
        $this->name = $name;
        $this->project = $project;
    }

    public function getId(): BookId
    {
        return BookId::fromString($this->id);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'navigation' => $this->navigation
        ];
    }
}
