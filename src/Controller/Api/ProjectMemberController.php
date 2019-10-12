<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Http\Response\ValidationErrorResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api/project/members")
 */
class ProjectMemberController extends AbstractController
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/{projectId}/invite", methods={"POST"})
     * @param string $projectId
     * @param $apiData
     * @param ProjectJsonConverter $converter
     * @return SuccessResponse
     */
    public function invite(string $projectId, $apiData, ProjectJsonConverter $converter)
    {
        $project = $this->loadProjectForManage($projectId);
        $users = $apiData['users'] ?? [];

        if (empty($users)) {
            return new SuccessResponse($converter->members($project));
        }

        $userEntities = $this->findUserEntities($users);

        foreach ($userEntities as $userEntity) {
            $project->addUser($userEntity);
        }

        $this->projectRepository->save($project);

        return new SuccessResponse($converter->members($project));
    }

    /**
     * @Route("/{projectId}/change-role", methods={"POST"}, name="api_project_member_change_role")
     * @param string $projectId
     * @param Request $request
     * @return ValidationErrorResponse|SuccessResponse
     */
    public function changeRole(string $projectId, Request $request) {
        $project = $this->loadProjectForManage($projectId);

        $apiData = $request->attributes->get('apiData');
        $user = $this->findUserEntity($apiData['member']);
        if ($project->getUserRole($user)->equals(Role::owner())) {
            throw $this->createAccessDeniedException();
        }

        $project->assignUserRole($user, Role::fromString($apiData['role']));
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }

    /**
     * @Route("/{projectId}/remove", methods={"POST"}, name="api_project_member_remove")
     * @param string $projectId
     * @param $apiData
     * @return ValidationErrorResponse|SuccessResponse
     */
    public function remove(string $projectId, $apiData) {
        $project = $this->loadProjectForManage($projectId);

        $user = $this->findUserEntity($apiData['member']);
        if ($project->getUserRole($user)->equals(Role::owner())) {
            throw $this->createAccessDeniedException();
        }

        $project->removeUser($user);
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }

    /**
     * @param string $projectId
     * @return Project
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    private function loadProjectForManage(string $projectId)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $exception) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        if (!$project->canManageUsers($user)) {
            throw $this->createAccessDeniedException();
        }

        return $project;
    }

    /**
     * @param string[] $usernames
     * @return User[]|object[]
     */
    private function findUserEntities(array $usernames)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        return $userRepository->findBy([
            'username' => $usernames
        ]);
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function findUserEntity(string $username)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        return $userRepository->findOneBy([
            'username' => $username
        ]);
    }
}
