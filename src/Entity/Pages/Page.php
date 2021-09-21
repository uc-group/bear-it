<?php

namespace App\Entity\Pages;

use App\Entity\Project;
use App\Pages\Command\PageEdit;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'bi_pages_page')]
class Page
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Project $project;

    public function __construct(string $id, string $name, string $content, Project $project)
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->project = $project;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'project' => $this->project->id()
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function update(PageEdit $pageEdit): void
    {
        $this->name = $pageEdit->getName();
        $this->content = $pageEdit->getContent();
    }
}
