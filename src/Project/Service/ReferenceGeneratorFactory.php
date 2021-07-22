<?php

namespace App\Project\Service;

use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;

class ReferenceGeneratorFactory
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function get(ProjectId $projectId)
    {
        return new ReferenceGenerator($projectId, $this->projectRepository);
    }
}
