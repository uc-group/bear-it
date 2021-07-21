<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bi_project')]
class Project
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 12)]
    private string $id;

    #[ORM\Column(type: 'string', length: 80)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', length: 7, nullable: true)]
    private ?string $color;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    public int $lastResourceId = 0;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json_array')]
    public array $components = [];

    public function __construct(string $id, string $name, string $description = null, string $color = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
    }

    public function id(): string
    {
        return $this->id;
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

    public function updateDescription(string $description = null)
    {
        $this->description = $description;
    }

    public function updateColor(string $color = null)
    {
        $this->color = $color;
    }
}
