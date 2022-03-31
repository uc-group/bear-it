<?php

namespace App\Project\Model\Project;

use App\Entity\Project as ProjectEntity;

class ProjectDescriptor
{
    private array $data = [
        'resources' => [],
        'files' => []
    ];

    private ProjectId $projectId;

    public function __construct(ProjectEntity $project) {
        $this->projectId = ProjectId::fromString($project->id());
        $this->data['id'] = $project->id();
        $this->data['name'] = $project->name();
        $this->data['color'] = $project->color();
        $this->data['description'] = $project->description();
        $this->data['components'] = $project->components;
    }

    public function getProjectId(): ProjectId
    {
        return $this->projectId;
    }

    public function getComponents(): array
    {
        return $this->data['components'];
    }

    public function getResources(): array
    {
        return $this->data['resources'];
    }

    public function getFiles(): array
    {
        return $this->data['files'];
    }

    public function toArray(): array
    {
        $data = $this->data;
        $data['resources'] = array_map(function ($resource)  {
            unset($resource['file_path']);

            return $resource;
        }, $data['resources']);

        $data['files'] = array_map(function (array $resource) {
            unset($resource['file_path']);

            return $resource;
        }, $data['files']);

        return $data;
    }

    public function addResource(ResourceId $id, string $filePath, string $path, array $meta = [])
    {
        $this->data['resources'][] = [
            'type' => get_class($id),
            'id' => $id->number(),
            'path' => $path,
            'file_path' => $filePath,
            'meta' => $meta
        ];
    }

    public function addFile(string $filePath, string $path, string $type, string $id, array $meta = [])
    {
        $this->data['files'][] = [
            'type' => $type,
            'id' => $id,
            'file_path' => $filePath,
            'path' => $path,
            'meta' => $meta
        ];
    }
}
