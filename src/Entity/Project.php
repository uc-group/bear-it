<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'bi_project')]
#[UniqueEntity('shortId')]
class Project
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 12)]
    private string $shortId;

    #[ORM\Column(type: 'string', length: 80)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', length: 7, nullable: true)]
    private ?string $color;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: Task::class)]
    private PersistentCollection $tasks;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json_array')]
    public array $components = [];

    public function __construct(string $id, string $shortId, string $name, string $description = null, string $color = null)
    {
        $this->id = $id;
        $this->shortId = $shortId;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->tasks = new PersistentCollection();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function shortId(): string
    {
        return $this->shortId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function color(): ?string
    {
        return $this->color;
    }

    public function rename(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Task[]|PersistentCollection
     */
    public function tasks(): PersistentCollection
    {
        return $this->tasks;
    }

    public function updateDescription(string $description = null)
    {
        $this->description = $description;
    }

    public function updateColor(string $color = null)
    {
        $this->color = $color;
    }
}
