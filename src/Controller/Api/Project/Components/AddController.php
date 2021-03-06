<?php

namespace App\Controller\Api\Project\Components;

use App\Http\Response\SuccessResponse;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;
use App\RequestValidator\Project\CategoryManage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/project/components/{id}/add')]
#[RequestValidator(CategoryManage::class)]
class AddController extends AbstractController
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(string $id, string $component)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($id));
        } catch (ProjectNotFoundException | InvalidProjectIdException $e) {
            throw $this->createNotFoundException();
        }

        $project->assignComponent($component);

        $this->projectRepository->save($project);
        $this->eventDispatcher->dispatch(new GenericEvent($project->id(), [
            'component' => $component
        ]), 'bearit.project.component_added');


        return new SuccessResponse($project->components());
    }
}
