<?php


namespace App\Controller\Api\Task;

use App\Entity\Project as ProjectEntity;
use App\Entity\Task;
use App\Http\Response\SuccessResponse;
use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;
use App\RequestValidator\Task\Create;
use App\Task\Model\Task\TaskId;
use App\Task\Repository\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/task/create', name: 'api_task_create')]
#[RequestValidator(Create::class)]
class CreateController extends AbstractController
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private TaskRepositoryInterface $taskRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(string $title, string $description, ProjectId $projectId): JsonResponse
    {
        $project = $this->projectRepository->load($projectId);
        $taskId = TaskId::create($project->shortId(), $this->taskRepository->nextTaskNumber($project));
        $projectEntityReference = $this->entityManager->getReference(
            ProjectEntity::class, $project->id()->toString());
        $task = new Task($taskId, $projectEntityReference, $title, 'new', $this->getUser());
        $task->setDescription($description);

        $this->taskRepository->save($task);

        return new SuccessResponse([
            'id' => $task->getId()->toString()
        ]);
    }
}