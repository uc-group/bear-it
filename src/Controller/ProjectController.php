<?php

namespace App\Controller;

use App\Entity\User;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Utils\KeyPrioritizedCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/project")
  */
class ProjectController extends AbstractController
{
    use ControllerTrait;

    /**
     * @param Request $request
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @Route("/create")
     */
    public function create(Request $request, ProjectRepositoryInterface $repository)
    {
        //TODO: validation
        $data = json_decode($request->getContent(), true);
        $id = ProjectId::fromString($data['id']);
        $project = new Project($id, $data['name'], $data['description']);
        $project->assignUserRole($this->getUser(), Role::owner());
        $repository->save($project);

        return new JsonResponse([
            'status' => 'OK',
            'data' => [
                'id' => $project->id()->toString()
            ]
        ]);
    }

    /**
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @Route("/user-list")
     */
    public function userList(ProjectRepositoryInterface $repository)
    {
        $projects = [];
        foreach ($repository->findByUser($this->getUser(), 100, 0) as $project) {
            $projects[] = [
                'id' => $project->id()->toString(),
                'name' => $project->name(),
                'description' => $project->description()
            ];
        }

        return new JsonResponse([
            'status' => 'OK',
            'data' => $projects
        ]);
    }

    /**
     * @param string $id
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @throws ProjectNotFoundException
     * @Route("/details/{id}")
     */
    public function project(string $id, ProjectRepositoryInterface $repository)
    {
        $project = $repository->load(ProjectId::fromString($id));

        $userIds = [];
        $members = [];
        foreach ($project->roles() as $userId => $role) {
            $userIds[] = $userId;
            $members[$userId] = [
                'role' => $role->toString()
            ];
        }

        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([
            'id' => $userIds
        ]);

        $memberCollection = new KeyPrioritizedCollection('role', ['owner', 'admin', 'member']);
        foreach ($users as $user) {
            $member = &$members[$user->getId()];
            $member['name'] = $user->getName();
            $member['username'] = $user->getUsername();
            $member['avatar'] = $user->getAvatar();
            $memberCollection->add($member);
        }
        unset($member);

        return new JsonResponse([
            'status' => 'OK',
            'data' => [
                'id' => $project->id()->toString(),
                'name' => $project->name(),
                'description' => $project->description(),
                'members' => $memberCollection->toSortedArray('name')
            ]
        ]);
    }
}
