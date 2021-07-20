<?php

namespace App\Doctrine\Repository;

use App\Entity\Project as ProjectEntity;
use App\Entity\ProjectUser;
use App\Entity\Task;
use App\Entity\User;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use BearIt\User\Model\UserId;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Project $project
     * @throws ORMException
     * @throws ConnectionException
     */
    public function save(Project $project)
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        $this->updateProjectEntity($project);
        if ($project->rolesChanged()) {
            $this->updateRoles($project->id(), $project->roles());
        }

        $connection->commit();
    }

    /**
     * @param ProjectId $projectId
     * @return Project
     * @throws ProjectNotFoundException
     * @throws \App\Project\Exception\InvalidProjectIdException
     */
    public function load(ProjectId $projectId): Project
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p');
        $qb->from(ProjectEntity::class, 'p');
        $qb->where('p.id = :id');
        $qb->setParameter('id', $projectId->toString());

        try {
            $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
            if (!$result) {
                throw ProjectNotFoundException::forId($projectId);
            }
            $roles = $this->findRolesByProject(ProjectId::fromString($result['id']));
            $tasks = $this->findTasksByProject(ProjectId::fromString($result['id']));

            return new Project(
                ProjectId::fromString($result['id']),
                $result['shortId'],
                $result['name'],
                $result['description'],
                $result['color'],
                $roles,
                $result['components'],
                $tasks
            );
        } catch (NonUniqueResultException) {
            throw ProjectNotFoundException::forId($projectId);
        }
    }

    /**
     * @param ProjectId $projectId
     * @return bool
     */
    public function remove(ProjectId $projectId): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete();
        $qb->from(ProjectEntity::class, 'p');
        $qb->where('p.id = :id');
        $qb->setParameter('id', $projectId->toString());
        $qb->getQuery()->execute();
    }

    /**
     * @return Project[]
     * @throws InvalidProjectIdException
     */
    public function findByUser(UserId $user, int $limit, int $offset): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from(ProjectEntity::class, 'p');
        $qb->select('p.id', 'p.shortId', 'p.name', 'p.description', 'p.color', 'p.components');
        $qb->leftJoin(ProjectUser::class, 'pu', Join::WITH, 'pu.project = p.id');
        $qb->where('pu.user = :user');
        $qb->setParameter('user', $user->toString());
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);
        $qb->orderBy('p.name');

        $projectData = $qb->getQuery()->getArrayResult();
        $projectIds = array_column($projectData, 'id');
        $roles = $this->findRolesByProjects($projectIds);
        $tasks = $this->findTasksByProjects($projectIds);

        $projects = [];
        foreach ($projectData as $row) {
            $id = $row['id'];
            $projects[] = new Project(
                ProjectId::fromString($id),
                $row['shortId'],
                $row['name'],
                $row['description'],
                $row['color'],
                $roles[$id] ?? [],
                $row['components'],
                $tasks[$id] ?? []
            );
        }

        return $projects;
    }

    /**
     * @param Project $project
     */
    private function updateProjectEntity(Project $project): void
    {
        $repository = $this->entityManager->getRepository(ProjectEntity::class);
        $projectEntity = $repository->find($project->id()->toString());
        if (!$projectEntity) {
            $projectEntity = new ProjectEntity(
                $project->id()->toString(),
                $project->shortId(),
                $project->name(),
                $project->description(),
                $project->color()
            );
        } else {
            $projectEntity->rename($project->name());
            $projectEntity->updateDescription($project->description());
            $projectEntity->updateColor($project->color());
        }
        $projectEntity->components = $project->components();

        $this->entityManager->persist($projectEntity);
        $this->entityManager->flush();
    }

    /**
     * @param ProjectId $id
     * @param Role[] $roles
     * @throws ORMException
     */
    private function updateRoles(ProjectId $id, $roles): void
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete();
        $qb->from(ProjectUser::class, 'pu');
        $qb->where('pu.project = :project');
        $qb->setParameter('project', $id->toString());
        $qb->getQuery()->execute();

        /** @var ProjectEntity $project */
        $project = $this->entityManager->getReference(ProjectEntity::class, $id->toString());
        foreach ($roles as $userId => $role) {
            /** @var User $user */
            $user = $this->entityManager->getReference(User::class, $userId);
            $roleEntity = new ProjectUser($user, $project, $role);
            $this->entityManager->persist($roleEntity);
        }
        $this->entityManager->flush();
    }

    /**
     * @param ProjectId $id
     * @return array
     */
    private function findRolesByProject(ProjectId $id)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from(ProjectUser::class, 'pu');
        $qb->select('IDENTITY(pu.user) AS userId', 'pu.role');
        $qb->where('pu.project = :project');
        $qb->setParameter('project', $id->toString());

        $roles = [];
        foreach ($qb->getQuery()->getArrayResult() as $row) {
            $userId = $row['userId'];
            $roles[$userId] = Role::fromString($row['role']);
        }

        return $roles;
    }

    /**
     * @param string[] $ids
     * @return array
     */
    private function findRolesByProjects(array $ids)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from(ProjectUser::class, 'pu');
        $qb->select('IDENTITY(pu.user) AS userId', 'IDENTITY(pu.project) AS projectId', 'pu.role');
        $qb->where($qb->expr()->in('pu.project', ':projects'));
        $qb->setParameter('projects', $ids);

        $roles = [];
        foreach ($qb->getQuery()->getArrayResult() as $row) {
            $userId = $row['userId'];
            $projectId = $row['projectId'];
            $roles[$projectId][$userId] = Role::fromString($row['role']);
        }

        return $roles;
    }

    private function findTasksByProject(ProjectId $id)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t');
        $qb->from(Task::class, 't');
        $qb->where('t.project = :project');
        $qb->setParameter('project', $id->toString());

        return $qb->getQuery()->getResult();
    }

    private function findTasksByProjects(array $projectIds)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t');
        $qb->from(Task::class, 't');
        $qb->where($qb->expr()->in('t.project', ':projectIds'));
        $qb->setParameter('projectIds', $projectIds);

        $result = [];
        /** @var Task $task */
        foreach ($qb->getQuery()->getResult() as $task) {
            $result[$task->getProject()->id()][] = $task;
        }

        return $result;
    }
}
