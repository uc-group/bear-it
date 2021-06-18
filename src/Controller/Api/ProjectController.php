<?php

namespace App\Controller\Api;

use App\Controller\Traits\ProjectUserTrait;
use App\Exception\UserNotLoggedInException;
use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Model\ProjectAccess\Policy\ProjectPolicy;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/project")
 */
class ProjectController extends AbstractController
{
    use ProjectUserTrait;

    /**
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @Route("/user-list")
     * @throws UserNotLoggedInException
     */
    public function userList(ProjectRepositoryInterface $repository)
    {
        $projects = [];
        foreach ($repository->findByUser($this->currentUserId(), 100, 0) as $project) {
            $projects[] = [
                'id' => $project->id()->toString(),
                'name' => $project->name(),
                'description' => $project->description(),
                'color' => $project->color()
            ];
        }

        return new SuccessResponse($projects);
    }

    /**
     * @param string $id
     * @param ProjectRepositoryInterface $repository
     * @param ProjectJsonConverter $converter
     * @return JsonResponse
     * @throws ProjectNotFoundException
     * @throws InvalidProjectIdException
     * @Route("/details/{id}")
     */
    public function project(string $id, ProjectRepositoryInterface $repository, ProjectJsonConverter $converter)
    {
        $project = $repository->load(ProjectId::fromString($id));

        return new SuccessResponse($converter->full($project));
    }

    /**
     * @param string $id
     * @param ProjectRepositoryInterface $repository
     * @return SuccessResponse
     * @throws UserNotLoggedInException
     * @Route("/remove/{id}")
     */
    public function remove(string $id, ProjectRepositoryInterface $repository)
    {
        try {
            $projectId = ProjectId::fromString($id);
            $project = $repository->load($projectId);
        } catch (ProjectNotFoundException | InvalidProjectIdException $exception) {
            throw $this->createNotFoundException();
        }

        $userAccess = $this->getUserAccess($project);

        if (!$userAccess->isGranted(ProjectPolicy::removeFunction(), $project)) {
            throw $this->createAccessDeniedException();
        }

        $repository->remove($projectId);

        return new SuccessResponse();
    }
}
