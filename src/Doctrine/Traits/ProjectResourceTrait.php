<?php

namespace App\Doctrine\Traits;

use App\Entity\Project as ProjectEntity;
use App\Project\Model\Project\ResourceId;
use Doctrine\ORM\EntityManagerInterface;

trait ProjectResourceTrait
{
    private EntityManagerInterface $entityManager;

    private function createProjectResource(ResourceId $resourceId)
    {
        $project = $this->entityManager->getReference(
            ProjectEntity::class,
            $resourceId->getProjectId()->toString()
        );

        $projectResource = new \App\Entity\ProjectResource(
            $project,
            $resourceId->number(),
            get_class($resourceId)
        );

        $this->entityManager->persist($projectResource);
    }
}