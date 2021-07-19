<?php

namespace App\JsonConverter;

use App\Entity\Task;

class TaskJsonConverter
{
    public function full(Task $task): array
    {
        return [
            'id' => $task->getId()->toString(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'project' => [
                'id' => $task->getProject()->id(),
                'shortId' => $task->getProject()->shortId()
            ]
        ];
    }
}