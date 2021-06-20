<?php

namespace App\Controller\Api\ProjectMember;

use App\Controller\Traits\ProjectUserTrait;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\ProjectAccess\Policy\ManageUsersPolicy;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Repository\UserRepository;
use App\RequestValidator\ProjectMember\Invite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/members/{projectId}/invite', name: 'api_project_member_invite', methods: ['POST'])]
#[RequestValidator(Invite::class)]
class InviteController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private UserRepository $userRepository,
        private ProjectRepositoryInterface $projectRepository,
        private ProjectJsonConverter $converter,
    ) {}

    public function __invoke(string $projectId, array $usernames): JsonResponse
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $e) {
            throw $this->createNotFoundException();
        }

        $userAccess = $this->getUserAccess($project);
        $this->throwAccessDeniedUnlessGranted($userAccess, ProjectPolicy::memberManageFunction(), $project);

        if (empty($usernames)) {
            return new SuccessResponse($this->converter->members($project));
        }

        $userEntities = $this->userRepository->findManyByUsernames($usernames);

        foreach ($userEntities as $userEntity) {
            $userId = $userEntity->getId();
            if ($userAccess->isGranted(ManageUsersPolicy::inviteFunction(), $userId)) {
                $project->addUser($userId);
            }
        }

        $this->projectRepository->save($project);

        return new SuccessResponse($this->converter->members($project));
    }
}
