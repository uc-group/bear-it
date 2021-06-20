<?php

namespace App\Controller\Api\ProjectMember;

use App\Controller\Traits\ProjectUserTrait;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\ProjectAccess\Policy\ManageUsersPolicy;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Repository\UserRepository;
use App\RequestValidator\ProjectMember\Remove;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/members/{projectId}/remove', name: 'api_project_member_remove', methods: ['POST'])]
#[RequestValidator(Remove::class)]
class RemoveController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private UserRepository $userRepository,
        private ProjectRepositoryInterface $projectRepository,
    ) {}

    public function __invoke(string $projectId, string $username)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $e) {
            throw $this->createNotFoundException();
        }

        $userAccess = $this->getUserAccess($project);
        $this->throwAccessDeniedUnlessGranted($userAccess, ProjectPolicy::memberManageFunction(), $project);

        $user = $this->userRepository->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $userId = $user->getId();
        $this->throwAccessDeniedUnlessGranted($userAccess, ManageUsersPolicy::removeFunction(), $userId);

        $project->removeUser($userId);
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }
}
