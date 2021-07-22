<?php

namespace App\Project\Service;

use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\ResourceId;
use App\Project\Repository\ProjectRepositoryInterface;

class ReferenceGenerator
{
    public function __construct(
        private ProjectId $projectId,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * @param int $count
     * @return ResourceId[]
     */
    public function generate(int $count = 1): array
    {
        $nextResourceId = $this->projectRepository->nextResourceId($this->projectId, $count);
        $generatedIds = [];
        for ($i = 0; $i < $count; $i++) {
            $generatedIds[] = ResourceId::create($this->projectId, $nextResourceId + $i);
        }

        return $generatedIds;
    }
}
