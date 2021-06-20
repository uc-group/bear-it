<?php

namespace App\Controller\Api\Project;

use App\Controller\Traits\ProjectUserTrait;
use App\Exception\UserNotLoggedInException;
use App\Http\Response\SuccessResponse;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use App\RequestValidator\Project\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/create', name: 'api_project_create')]
#[RequestValidator(Create::class)]
class CreateController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    /**
     * @throws UserNotLoggedInException
     */
    public function __invoke(Project $project): JsonResponse
    {
        $project->assignUserRole($this->currentUserId(), Role::owner());
        $this->repository->save($project);

        return new SuccessResponse([
            'id' => $project->id()->toString()
        ]);
    }
}
