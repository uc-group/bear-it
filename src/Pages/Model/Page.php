<?php

namespace App\Pages\Model;

use App\Project\Model\Project\ProjectId;

class Page
{
    public function __construct(
        private string $name,
        private string $content,
        private ProjectId $projectId
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }
}
