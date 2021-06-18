<?php

namespace App\Controller\Api;

use App\Controller\Traits\ProjectUserTrait;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Http\Response\ValidationErrorResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Model\ProjectAccess\Policy\ManageUsersPolicy;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/project/members")
 */
class ProjectMemberController extends AbstractController
{
    use ProjectUserTrait;

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
     * @Route("/{projectId}/change-role", methods={"POST"}, name="api_project_member_change_role")
     * @param string $projectId
     * @param Request $request
     * @return ValidationErrorResponse|SuccessResponse
     */
    public function changeRole(string $projectId, Request $request) {
        $project = $this->loadProjectForManage($projectId);
        $userAccess = $this->getUserAccess($project);
        $this->throwAccessDeniedUnlessGranted($userAccess, ProjectPolicy::memberManageFunction(), $project);

        $apiData = $request->attributes->get('apiData');
        $user = $this->findUserEntity($apiData['member']);
        $userId = $user->getId();
        $this->throwAccessDeniedUnlessGranted($userAccess, ManageUsersPolicy::changeRoleFunction(), $userId);
        $project->assignUserRole($userId, Role::fromString($apiData['role']));
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
        $userAccess = $this->getUserAccess($project);
        $this->throwAccessDeniedUnlessGranted($userAccess, ProjectPolicy::memberManageFunction(), $project);

        $user = $this->findUserEntity($apiData['member']);
        if (!$user) {
            throw $this->createNotFoundException();
        }
        $userId = $user->getId();
        $this->throwAccessDeniedUnlessGranted($userAccess, ManageUsersPolicy::removeFunction(), $userId);

        $project->removeUser($userId);
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }

    /**
     * @param string $projectId
     * @return Project
     */
    private function loadProjectForManage(string $projectId)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $exception) {
            throw $this->createNotFoundException();
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
     * @return User|object|null
     */
    private function findUserEntity(string $username)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        return $userRepository->findOneBy([
            'username' => $username
        ]);
    }
}
