<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'bi_project_resource')]
class ProjectResource
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $resourceNumber;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $resourceType;

    public function __construct(
        Project $project,
        int $resourceNumber,
        string $resourceType
    ) {
        $this->project = $project;
        $this->resourceNumber = $resourceNumber;
        $this->resourceType = $resourceType;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return int
     */
    public function getResourceNumber(): int
    {
        return $this->resourceNumber;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }
}
