<?php

namespace App\Entity;

use App\Project\Model\Project\Role;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bi_project_user")
 */
class ProjectUser
{
    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Project
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $project;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $role;

    /**
     * @param User $user
     * @param Project $project
     * @param Role $role
     */
    public function __construct(User $user, Project $project, Role $role)
    {
        $this->user = $user;
        $this->project = $project;
        $this->role = $role->toString();
    }
}