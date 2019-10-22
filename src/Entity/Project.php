<?php

namespace App\Entity;

use App\Project\Model\Project\ProjectId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bi_project")
 */
class Project
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true, length=7)
     */
    private $color;

    /**
     * @param string $id
     * @param string $name
     * @param string|null $description
     * @param string|null $color
     */
    public function __construct(string $id, string $name, string $description = null, string $color = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
    }

    /**
     * @return ProjectId
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function color(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $name
     */
    public function rename(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string|null $description
     */
    public function updateDescription(string $description = null)
    {
        $this->description = $description;
    }

    /**
     * @param string|null $color
     */
    public function updateColor(string $color = null)
    {
        $this->color = $color;
    }
}
