<?php

namespace App\Project\Exception;

use App\Project\Model\Project\ProjectId;

class ProjectNotFoundException extends \Exception
{
    /**
     * @param ProjectId $projectId
     * @return ProjectNotFoundException
     */
    public static function forId(ProjectId $projectId)
    {
        return new self(sprintf('Project with ID "%s" not found.', $projectId->toString()));
    }
}