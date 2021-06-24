<?php

namespace App\JsonConverter;

use App\Entity\User;
use App\Project\Model\Project\Project;
use App\Utils\KeyPrioritizedCollection;
use Doctrine\ORM\EntityManagerInterface;

class ProjectJsonConverter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ProjectJsonConverter constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Project $project
     * @return array
     */
    public function full(Project $project)
    {
        return [
            'id' => $project->id()->toString(),
            'name' => $project->name(),
            'description' => $project->description(),
            'members' => $this->members($project),
            'components' => $project->components(),
            'color' => $project->color()
        ];
    }

    /**
     * @param Project $project
     * @return array
     */
    public function members(Project $project)
    {
        $members = [];
        $userIds = [];
        foreach ($project->roles() as $userId => $role) {
            $userIds[] = $userId;
            $members[$userId] = [
                'role' => $role->toString()
            ];
        }

        if (empty($userIds)) {
            return [];
        }

        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findBy([
            'id' => $userIds
        ]);

        $memberCollection = new KeyPrioritizedCollection('role', ['owner', 'admin', 'member']);
        foreach ($users as $user) {
            $member = &$members[$user->getId()->toString()];
            $member['name'] = $user->getName();
            $member['username'] = $user->getUserIdentifier();
            $member['avatar'] = $user->getAvatar();
            $memberCollection->add($member);
        }
        unset($member);

        return $memberCollection->toSortedArray('name');
    }
}
