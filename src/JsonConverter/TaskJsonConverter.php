<?php

namespace App\JsonConverter;

use App\Task\Model\Task\Task;

class TaskJsonConverter
{
    public function full(Task $task): array
    {
        return [
            'id' => $task->id()->toString(),
            'title' => $task->title(),
            'description' => $task->description(),
            'project' => [
                'id' => $task->id()->getProjectId()->toString()
            ]
        ];
    }
}
