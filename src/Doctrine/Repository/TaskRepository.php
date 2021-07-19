<?php

namespace App\Doctrine\Repository;

use App\Entity\Task;
use App\Project\Model\Project\Project;
use App\Task\Model\Task\TaskId;
use App\Task\Repository\TaskRepositoryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function load(TaskId $taskId): ?Task
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t');
        $queryBuilder->from(Task::class, 't');
        $queryBuilder->where('t.id = :id');
        $queryBuilder->setParameter('id', $taskId->toString());

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function save(Task $task)
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function nextTaskNumber(Project $project): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t.id');
        $queryBuilder->from(Task::class, 't');
        $queryBuilder->where('t.project = :project');
        $queryBuilder->setParameter('project', $project->id()->toString());
        $queryBuilder->orderBy('t.createdAt', Criteria::DESC);
        $queryBuilder->setMaxResults(1);

        try {
            $newestId = $queryBuilder->getQuery()->getSingleScalarResult();
            $taskNumber = str_replace(sprintf("%s-", $project->shortId()), '', $newestId);

            return (int) $taskNumber + 1;
        } catch (NoResultException|NonUniqueResultException) {
            return 1;
        }
    }
}