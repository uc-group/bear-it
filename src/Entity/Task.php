<?php

namespace App\Entity;

use App\Exception\InvalidIdFormatException;
use App\Utils\DateTime;
use App\ValueObject\Estimation;
use App\ValueObject\Id\Task as TaskId;
use DateTime as PhpDateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bi_task")
 */
class Task
{
    const UNIT_MAN_HOURS = 'mh';
    const UNIT_MAN_DAYS = 'md';
    const UNIT_STORY_POINTS = 'sp';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     */
    private $id;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $reporter;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $assignee;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $creator;

    /**
     * @var string
     * @ORM\Column(type="string", length=80)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=36)
     */
    private $status;

    /**
     * @var PhpDateTime|null
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $estimation;

    /**
     * @var string
     * @ORM\Column(type="string", length=10)
     */
    private $estimationUnit;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $resolved;

    /**
     * @param TaskId $id
     * @param string $title
     * @param string $status
     * @param User $creator
     * @param User|null $reporter
     */
    public function __construct(TaskId $id, string $title, string $status, User $creator, User $reporter = null)
    {
        $this->id = $id->toString();
        $this->title = $title;
        $this->createdAt = DateTime::now();
        $this->creator = $creator;
        $this->reporter = $reporter ?? $creator;
        $this->status = $status;
        $this->resolved = false;
    }

    /**
     * @return TaskId
     */
    public function getId(): ?TaskId
    {
        try {
            return TaskId::fromString($this->id);
        } catch (InvalidIdFormatException $exception) {
            return TaskId::create(TaskId::PREFIX_INVALID, 0);
        }
    }

    /**
     * @param User $user
     */
    public function assignTo(User $user)
    {
        $this->assignee = $user;
    }

    /**
     * @return User
     */
    public function getAssignee(): User
    {
        return $this->assignee;
    }

    /**
     * @param User $reporter
     */
    public function setReporter(User $reporter): void
    {
        $this->reporter = $reporter;
    }

    /**
     * @return User|null
     */
    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * @param Estimation $estimation
     */
    public function estimate(Estimation $estimation): void
    {
        $this->estimation = $estimation->getValue();
        $this->estimationUnit = $estimation->getUnit();
    }

    /**
     * @return Estimation
     */
    public function getEstimation(): Estimation
    {
        return Estimation::create($this->estimation, $this->estimationUnit);
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function resolve(): void
    {
        $this->resolved = true;
    }
}