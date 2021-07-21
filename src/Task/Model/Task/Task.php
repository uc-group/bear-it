<?php

namespace App\Task\Model\Task;

class Task
{
    public function __construct(
        private TaskId $id,
        private Creator $creator,
        private string $title,
        private Status $status,
        private \DateTime $createdAt,
        private ?Reporter $reporter,
        private ?Assignee $assignee,
        private ?string $description
    ) {}

    public function id(): TaskId
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function creator(): Creator
    {
        return $this->creator;
    }

    public function assignee(): ?Assignee
    {
        return $this->assignee;
    }

    public function reporter(): ?Reporter
    {
        return $this->reporter;
    }

    public function status(): Status
    {
        return $this->status;
    }

    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

}
