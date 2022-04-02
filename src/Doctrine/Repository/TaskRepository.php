<?php

namespace App\Doctrine\Repository;

use App\Doctrine\Traits\ProjectResourceTrait;
use App\Entity\Task as TaskEntity;
use App\Entity\User;
use App\Task\Model\Task\Assignee;
use App\Task\Model\Task\BoundedUser;
use App\Task\Model\Task\Creator;
use App\Task\Model\Task\Reporter;
use App\Task\Model\Task\Status;
use App\Task\Model\Task\Task;
use App\Task\Model\Task\TaskId;
use App\Task\Repository\TaskRepositoryInterface;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class TaskRepository implements TaskRepositoryInterface
{
    use ProjectResourceTrait;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function load(TaskId $taskId): ?Task
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t');
        $queryBuilder->from(TaskEntity::class, 't');
        $queryBuilder->where('t.resource.number = :number');
        $queryBuilder->andWhere('t.resource.project = :project');
        $queryBuilder->setParameter('number', $taskId->number());
        $queryBuilder->setParameter('project', $taskId->getProjectId()->toString());

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

    /**
     * @throws ORMException
     * @throws DBALException
     */
    public function save(Task $task)
    {
        $repository = $this->entityManager->getRepository(TaskEntity::class);
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        $taskId = $task->id();
        $entity = $repository->findBy([
            'resource.number' => $taskId->number(),
            'resource.project' => $taskId->getProjectId()->toString(),
        ]);
        if (!$entity) {
            $this->createProjectResource($taskId);

            $entity = new TaskEntity(
                $taskId,
                $task->title(),
                $task->status()->toString(),
                $this->getUserReference($task->creator())
            );
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
     * @param BoundedUser|null $user
     * @return User|object
     * @throws ORMException
     */
    private function getUserReference(?BoundedUser $user): ?User
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
