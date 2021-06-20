<?php

namespace App\Controller\Api\Project\Components;

use App\Http\Response\SuccessResponse;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;
use App\RequestValidator\Project\CategoryManage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/components/{id}/remove')]
#[RequestValidator(CategoryManage::class)]
class RemoveController extends AbstractController
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function __invoke(string $id, string $component)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($id));
        } catch (ProjectNotFoundException | InvalidProjectIdException $e) {
            throw $this->createNotFoundException();
        }

        $project->removeComponent($component);

        $this->projectRepository->save($project);

        return new SuccessResponse($project->components());
    }
}
