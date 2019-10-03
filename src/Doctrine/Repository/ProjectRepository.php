<?php

namespace App\Doctrine\Repository;

use App\Entity\Project as ProjectEntity;
use App\Entity\ProjectUser;
use App\Entity\User;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
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
     */
    public function load(ProjectId $projectId): Project
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from(ProjectEntity::class, 'p');
        $qb->where('p.id = :id');
        $qb->setParameter('id', $projectId->toString());

        try {
            $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
            if (!$result) {
                throw ProjectNotFoundException::forId($projectId);
            }
            $roles = $this->findRolesByProject($result['id']);

            return new Project(
                ProjectId::fromString($result['id']),
                $result['name'],
                $result['description'],
                $roles
            );
        } catch (NonUniqueResultException $exception) {
            throw ProjectNotFoundException::forId($projectId);
        }
    }

    /**
     * @param User $user
     * @param int $limit
     * @param int $offset
     * @return Project[]
     */
    public function findByUser(User $user, int $limit, int $offset)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->from(ProjectEntity::class, 'p');
        $qb->select('p.id', 'p.name', 'p.description');
        $qb->leftJoin(ProjectUser::class, 'pu', Join::WITH, 'pu.project = p.id');
        $qb->where('pu.user = :user');
        $qb->setParameter('user', $user);
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);
        $qb->orderBy('p.name');

        $projectData = $qb->getQuery()->getArrayResult();
        $projectIds = array_column($projectData, 'id');
        $roles = $this->findRolesByProjects($projectIds);

        $projects = [];
        foreach ($projectData as $row) {
            $id = $row['id'];
            $projects[] = new Project(
                ProjectId::fromString($id),
                $row['name'],
                $row['description'],
                $roles[$id] ?? []
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
                $project->name(),
                $project->description()
            );
        } else {
            $projectEntity->rename($project->name());
            $projectEntity->updateDescription($projectEntity->description());
        }

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
}
