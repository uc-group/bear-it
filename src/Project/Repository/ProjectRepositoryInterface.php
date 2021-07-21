<?php

namespace App\Project\Repository;

use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use BearIt\User\Model\UserId;

interface ProjectRepositoryInterface
{
    public function save(Project $project);

    /**
     * @throws ProjectNotFoundException
     */
    public function load(ProjectId $projectId): Project;

    /**
     * @return Project[]
     */
    public function findByUser(UserId $userId, int $limit, int $offset): array;

    public function remove(ProjectId $projectId): void;

    public function nextResourceId(ProjectId $projectId, int $reserveCount = 1): int;
}
