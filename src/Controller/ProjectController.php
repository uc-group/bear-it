<?php

namespace App\Controller;

use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use App\Utils\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    use ControllerTrait;

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
}