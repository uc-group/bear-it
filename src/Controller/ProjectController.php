<?php

namespace App\Controller;

use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/create")
     */
    public function create(Request $request, ProjectRepositoryInterface $repository)
    {
        //TODO: validation
        $data = json_decode($request->getContent(), true);
        $project = new Project(ProjectId::new(), $data['name'], $data['description']);
        $project->assignUserRole($this->getUser(), Role::owner());
        $repository->save($project);

        return new JsonResponse([
            'status' => 'OK',
            'data' => [
                'id' => $project->id()->toString()
            ]
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

        return new JsonResponse([
            'status' => 'OK',
            'data' => $projects
        ]);
    }
}
