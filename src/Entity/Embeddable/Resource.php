<?php

namespace App\Entity\Embeddable;

use App\Project\Exception\InvalidProjectIdException;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\ResourceId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Resource
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $number;

    #[ORM\Column(type: 'string', length: 12)]
    private ?string $project;

    public function __construct(ProjectId $projectId, int $number) {
        $this->project = $projectId->toString();
        $this->number = $number;
    }

    public static function fromId(ResourceId $resourceId): Resource
    {
        return new self($resourceId->getProjectId(), $resourceId->number());
    }

    /**
     * @throws InvalidProjectIdException
     */
    public function id(): ResourceId
    {
        return ResourceId::create(ProjectId::fromString($this->project), $this->number);
    }
}