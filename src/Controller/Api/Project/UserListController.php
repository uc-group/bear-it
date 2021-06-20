<?php

namespace App\Controller\Api\Project;

use App\Controller\Traits\ProjectUserTrait;
use App\Exception\UserNotLoggedInException;
use App\Http\Response\SuccessResponse;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/project/user-list')]
class UserListController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    /**
     * @throws UserNotLoggedInException
     */
    public function __invoke(): JsonResponse
    {
        $projects = [];
        foreach ($this->repository->findByUser($this->currentUserId(), 100, 0) as $project) {
            $projects[] = [
                'id' => $project->id()->toString(),
                'name' => $project->name(),
                'description' => $project->description(),
                'color' => $project->color(),
                'components' => $project->components()
            ];
        }

        return new SuccessResponse($projects);
    }
}
