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
     * @param string $id
     * @param string $name
     * @param string|null $description
     */
    public function __construct(string $id, string $name, string $description = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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
}
