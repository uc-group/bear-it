<?php

namespace App\Controller\Api\Project;

use App\Controller\Traits\ProjectUserTrait;
use App\Http\Response\SuccessResponse;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/project/remove/{id}')]
class RemoveController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $projectId = ProjectId::fromString($id);
            $project = $this->repository->load($projectId);
        } catch (ProjectNotFoundException | InvalidProjectIdException $exception) {
            throw $this->createNotFoundException();
        }

        $userAccess = $this->getUserAccess($project);

        if (!$userAccess->isGranted(ProjectPolicy::removeFunction(), $project)) {
            throw $this->createAccessDeniedException();
        }

        $this->repository->remove($projectId);

        return new SuccessResponse();
    }
}
