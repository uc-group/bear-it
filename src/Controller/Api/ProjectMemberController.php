<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\ProjectId;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/project/members")
 */
class ProjectMemberController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/{projectId}/invite", methods={"POST"})
     * @param string $projectId
     * @param Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @param ProjectJsonConverter $converter
     * @return SuccessResponse
     */
    public function invite(
        string $projectId,
        Request $request,
        ProjectRepositoryInterface $projectRepository,
        ProjectJsonConverter $converter
    ) {
        try {
            $project = $projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException $exception) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        if (!$project->canManageUsers($user)) {
            throw $this->createAccessDeniedException();
        }

        $data = json_decode($request->getContent(), true);
        $users = $data['users'] ?? [];

        if (empty($users)) {
            return new SuccessResponse($converter->members($project));
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User[] $userEntities */
        $userEntities = $userRepository->findBy([
            'username' => $users
        ]);

        foreach ($userEntities as $userEntity) {
            $project->addUser($userEntity);
        }

        $projectRepository->save($project);

        return new SuccessResponse($converter->members($project));
    }
}
