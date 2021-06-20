<?php

namespace App\Controller\Api\Project;

use App\Controller\Traits\ProjectUserTrait;
use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/project/details/{id}')]
class DetailsController extends AbstractController
{
    use ProjectUserTrait;

    public function __construct(
        private ProjectRepositoryInterface $repository,
        private ProjectJsonConverter $converter
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $project = $this->repository->load(ProjectId::fromString($id));

        return new SuccessResponse($this->converter->full($project));
    }
}
