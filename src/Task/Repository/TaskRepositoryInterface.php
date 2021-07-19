<?php

namespace App\Task\Repository;

use App\Entity\Task;
use App\Project\Model\Project\Project;
use App\Task\Model\Task\TaskId;

interface TaskRepositoryInterface
{
    public function load(TaskId $taskId): ?Task;

    public function save(Task $task);

    public function nextTaskNumber(Project $project): int;
}