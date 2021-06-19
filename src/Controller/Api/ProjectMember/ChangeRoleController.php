<?php

namespace App\Controller\Api\ProjectMember;

use App\Controller\Traits\ProjectUserTrait;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Model\ProjectAccess\Policy\ManageUsersPolicy;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Repository\UserRepository;
use App\RequestValidator\ProjectMember\ChangeRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/members/{projectId}/change-role', name: 'api_project_member_change_role', methods: ['POST'])]
#[RequestValidator(ChangeRole::class)]
class ChangeRoleController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private UserRepository $userRepository,
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function __invoke(string $projectId, string $member, Role $role)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $e) {
            throw $this->createNotFoundException();
        }

        $userAccess = $this->getUserAccess($project);
        $this->throwAccessDeniedUnlessGranted($userAccess, ProjectPolicy::memberManageFunction(), $project);
        $user = $this->userRepository->findOneByUsername($member);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $userId = $user->getId();
        $this->throwAccessDeniedUnlessGranted($userAccess, ManageUsersPolicy::changeRoleFunction(), $userId);
        $project->assignUserRole($userId, $role);
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }
}
