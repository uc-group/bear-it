<?php


namespace App\Controller\Api\Task;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Project\Model\Project\ProjectId;
use App\Project\Service\ReferenceGeneratorFactory;
use App\RequestValidator\Task\Create;
use App\Task\Model\Task\Creator;
use App\Task\Model\Task\Reporter;
use App\Task\Model\Task\Status;
use App\Task\Model\Task\Task;
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
        private ReferenceGeneratorFactory $referenceGeneratorFactory,
        private TaskRepositoryInterface $taskRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(string $title, string $description, ProjectId $projectId): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->entityManager->beginTransaction();
        $referenceGenerator = $this->referenceGeneratorFactory->get($projectId);
        $id = $referenceGenerator->generate()[0];
        $taskId = TaskId::fromString($id->toString());

        $this->taskRepository->save(new Task(
            $taskId,
            Creator::fromUserId($user->getId()),
            $title,
            Status::new(),
            new \DateTime(),
            Reporter::fromUserId($user->getId()),
            null,
            $description
        ));
        $this->entityManager->commit();

        return new SuccessResponse([
            'id' => $taskId->toString()
        ]);
    }
}
