<?php

namespace App\Project\Repository;

use App\Entity\User;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use BearIt\User\Model\UserId;

interface ProjectRepositoryInterface
{
    /**
     * @param Project $project
     */
    public function save(Project $project);

    /**
     * @param ProjectId $projectId
     * @return Project
     * @throws ProjectNotFoundException
     */
    public function load(ProjectId $projectId): Project;

    /**
     * @param UserId $userId
     * @param int $limit
     * @param int $offset
     * @return Project[]
     */
    public function findByUser(UserId $userId, int $limit, int $offset);

    /**
     * @param ProjectId $projectId
     */
    public function remove(ProjectId $projectId): void;
}
