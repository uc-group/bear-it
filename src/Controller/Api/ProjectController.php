<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Utils\KeyPrioritizedCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/project")
 */
class ProjectController extends AbstractController
{
    use ControllerTrait;

    /**
     * @param Request $request
     * @param ProjectRepositoryInterface $repository
     * @return JsonResponse
     * @throws \App\Project\Exception\InvalidProjectIdException
     * @Route("/create")
     */
    public function create(Request $request, ProjectRepositoryInterface $repository)
    {
        //TODO: validation
        $data = json_decode($request->getContent(), true);
        $id = ProjectId::fromString($data['id']);
        $project = new Project($id, $data['name'], $data['description']);
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
     * @throws \App\Project\Exception\InvalidProjectIdException
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
     * @throws ProjectNotFoundException
     * @throws \App\Project\Exception\InvalidProjectIdException
     * @Route("/remove/{id}")
     */
    public function remove(string $id, ProjectRepositoryInterface $repository)
    {
        $user = $this->getUser();
        $project = $repository->load(ProjectId::fromString($id));
        if (!$project->canRemove($user)) {
            throw $this->createAccessDeniedException();
        }

        $removed = $repository->remove(ProjectId::fromString($id));
        $message = $removed ? 'Project successfully removed.' : 'Remove error.';

        return new SuccessResponse(['message' => $message]);
    }
}
