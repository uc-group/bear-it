<?php

namespace App\Controller\Api\Task;

use App\Http\Response\SuccessResponse;
use App\JsonConverter\TaskJsonConverter;
use App\Project\Model\Project\ProjectId;
use App\Task\Model\Task\TaskId;
use App\Task\Repository\TaskRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/task/details/{id}')]
class DetailsController extends AbstractController
{
    public function __construct(
        private TaskRepositoryInterface $repository,
        private TaskJsonConverter $converter
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $task = $this->repository->load(TaskId::fromString($id));

        return new SuccessResponse($this->converter->full($task));
    }
}
