<?php

namespace App\Task\Repository;

use App\Task\Model\Task\Task;
use App\Task\Model\Task\TaskId;

interface TaskRepositoryInterface
{
    public function load(TaskId $taskId): ?Task;
    public function save(Task $task);
}
