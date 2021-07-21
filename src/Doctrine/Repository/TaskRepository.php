<?php

namespace App\Doctrine\Repository;

use App\Entity\Project as ProjectEntity;
use App\Entity\ProjectResource;
use App\Entity\Task as TaskEntity;
use App\Entity\User;
use App\Task\Model\Task\Assignee;
use App\Task\Model\Task\BoundUser;
use App\Task\Model\Task\Creator;
use App\Task\Model\Task\Reporter;
use App\Task\Model\Task\Status;
use App\Task\Model\Task\Task;
use App\Task\Model\Task\TaskId;
use App\Task\Repository\TaskRepositoryInterface;
use App\User\Model\User\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function load(TaskId $taskId): ?Task
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t');
        $queryBuilder->from(TaskEntity::class, 't');
        $queryBuilder->where('t.id = :id');
        $queryBuilder->setParameter('id', $taskId->toString());

        try {
            /** @var TaskEntity $entity */
            $entity = $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }

        return $entity ? new Task(
            $entity->getId(),
            Creator::fromUserId($entity->getCreator()->getId()),
            $entity->getTitle(),
            $entity->getStatus(),
            $entity->getCreatedAt(),
            $entity->getReporter() ? Reporter::fromUserId($entity->getReporter()->getId()) : null,
            $entity->getAssignee() ? Assignee::fromUserId($entity->getAssignee()->getId()) : null,
            $entity->getDescription()
        ) : null;
    }

    public function save(Task $task)
    {
        $repository = $this->entityManager->getRepository(TaskEntity::class);
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        $taskId = $task->id();
        $entity = $repository->find($taskId->toString());
        if (!$entity) {
            /** @var ProjectEntity $project */
            $project = $this->entityManager->getReference(
                ProjectEntity::class,
                $taskId->getProjectId()->toString()
            );

            $entity = new TaskEntity(
                $taskId,
                $project,
                $task->title(),
                $task->status()->toString(),
                $this->getUserReference($task->creator())
            );

            $projectResource = new ProjectResource(
                $project,
                $taskId->number(),
                TaskId::class
            );
            $this->entityManager->persist($projectResource);
        } else {
            $entity->setStatus($task->status()->toString());
        }

        $entity->assignTo($this->getUserReference($task->assignee()));
        $entity->setReporter($this->getUserReference($task->reporter()));
        $entity->setDescription($task->description());

        if ($task->status()->equals(Status::closed())) {
            $entity->resolve();
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $connection->commit();
    }

    /**
     * @param UserId $id
     * @return User|object
     */
    private function getUserReference(?BoundUser $user): ?User
    {
        if (!$user) {
            return null;
        }

        return $this->entityManager->getReference(
            User::class,
            $user->getUserId()->toString()
        );
    }
}
