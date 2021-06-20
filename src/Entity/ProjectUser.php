<?php

namespace App\Entity;

use App\Project\Model\Project\Role;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bi_project_user')]
class ProjectUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Project $project;

    #[ORM\Column(type: 'string', length: 20)]
    private string $role;

    public function __construct(User $user, Project $project, Role $role)
    {
        $this->user = $user;
        $this->project = $project;
        $this->role = $role->toString();
    }
}
