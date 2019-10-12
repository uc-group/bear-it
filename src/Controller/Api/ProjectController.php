<?php

namespace App\Controller\Api;

use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @param ProjectRepositoryInterface $repository
     * @param $apiData
     * @return JsonResponse
     * @throws InvalidProjectIdException
     * @Route("/create", name="api_project_create")
     */
    public function create(ProjectRepositoryInterface $repository, $apiData)
    {
        $id = ProjectId::fromString($apiData['id']);
        $project = new Project($id, $apiData['name'], $apiData['description']);
        $project->assignUserRole($this->getUser(), Role::owner());
        $repository->save($project);

        return new SuccessResponse([
            'id' => $project->id()->toString()
        ]);
    }

    /**
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @Route("/user-list")
     */
    public function userList(ProjectRepositoryInterface $repository)
    {
        $projects = [];
        foreach ($repository->findByUser($this->getUser(), 100, 0) as $project) {
            $projects[] = [
                'id' => $project->id()->toString(),
                'name' => $project->name(),
                'description' => $project->description()
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
}
