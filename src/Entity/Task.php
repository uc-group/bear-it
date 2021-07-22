<?php

namespace App\Entity;

use App\Exception\InvalidIdFormatException;
use App\Task\Model\Task\Status;
use App\Utils\DateTime;
use App\ValueObject\Estimation;
use App\Task\Model\Task\TaskId;
use DateTime as PhpDateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bi_task')]
class Task
{
    const UNIT_MAN_HOURS = 'mh';
    const UNIT_MAN_DAYS = 'md';
    const UNIT_STORY_POINTS = 'sp';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reporter_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $reporter;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'assignee_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $assignee;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $creator;

    #[ORM\Column(type: 'string', length: 80)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 36)]
    private string $status;

    #[ORM\Column(type: 'datetime')]
    private ?PhpDateTime $createdAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $estimation;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $estimationUnit;

    #[ORM\Column(type: 'boolean')]
    private bool $resolved;

    public function __construct(TaskId $taskId, Project $project, string $title, string $status, User $creator)
    {
        $this->id = $taskId->toString();
        $this->project = $project;
        $this->title = $title;
        $this->createdAt = DateTime::now();
        $this->creator = $creator;
        $this->reporter = $creator;
        $this->status = $status;
        $this->resolved = false;
    }

    public function getId(): ?TaskId
    {
        try {
            return TaskId::fromString($this->id);
        } catch (InvalidIdFormatException) {
            return TaskId::create(TaskId::PREFIX_INVALID, 0);
        }
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function assignTo(?User $user)
    {
        $this->assignee = $user;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setReporter(?User $reporter): void
    {
        $this->reporter = $reporter;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    public function getStatus(): Status
    {
        return Status::fromString($this->status);
    }

    public function getCreatedAt(): PhpDateTime
    {
        return $this->createdAt;
    }

    public function estimate(Estimation $estimation): void
    {
        $this->estimation = $estimation->getValue();
        $this->estimationUnit = $estimation->getUnit();
    }

    public function getEstimation(): Estimation
    {
        return Estimation::create($this->estimation, $this->estimationUnit);
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function resolve(): void
    {
        $this->resolved = true;
    }
}
