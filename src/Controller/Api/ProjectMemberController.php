<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\Http\Response\ValidationErrorResponse;
use App\JsonConverter\ProjectJsonConverter;
use App\Project\Exception\InvalidProjectIdException;
use App\Project\Exception\ProjectNotFoundException;
use App\Project\Model\Project\Project;
use App\Project\Model\Project\ProjectId;
use App\Project\Model\Project\Role;
use App\Project\Repository\ProjectRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/project/members")
 */
class ProjectMemberController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepository;

    /**
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/{projectId}/invite", methods={"POST"})
     * @param string $projectId
     * @param Request $request
     * @param ProjectJsonConverter $converter
     * @return SuccessResponse
     */
    public function invite(string $projectId, Request $request, ProjectJsonConverter $converter)
    {
        $project = $this->loadProjectForManage($projectId);
        $data = json_decode($request->getContent(), true);
        $users = $data['users'] ?? [];

        if (empty($users)) {
            return new SuccessResponse($converter->members($project));
        }

        $userEntities = $this->findUserEntities($users);

        foreach ($userEntities as $userEntity) {
            $project->addUser($userEntity);
        }

        $this->projectRepository->save($project);

        return new SuccessResponse($converter->members($project));
    }

    /**
     * @Route("/{projectId}/change-role", methods={"POST"})
     * @param string $projectId
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return ValidationErrorResponse|SuccessResponse
     */
    public function changeRole(string $projectId, Request $request, ValidatorInterface $validator) {
        $project = $this->loadProjectForManage($projectId);

        $data = json_decode($request->getContent(), true);
        $errors = $validator->validate($data, new Constraints\Collection([
            'member' => [new Constraints\NotNull()],
            'role' => [new Constraints\Choice([
                'choices' => [
                    Role::admin()->toString(),
                    Role::member()->toString()
                ]
            ])]
        ]));

        if ($errors->count() > 0) {
            return new ValidationErrorResponse($errors);
        }

        $user = $this->findUserEntity($data['member']);
        if ($project->getUserRole($user)->equals(Role::owner())) {
            throw $this->createAccessDeniedException();
        }

        $project->assignUserRole($user, Role::fromString($data['role']));
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }

    /**
     * @Route("/{projectId}/remove", methods={"POST"})
     * @param string $projectId
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return ValidationErrorResponse|SuccessResponse
     */
    public function remove(string $projectId, Request $request, ValidatorInterface $validator) {
        $project = $this->loadProjectForManage($projectId);

        $data = json_decode($request->getContent(), true);
        $errors = $validator->validate($data, new Constraints\Collection([
            'member' => [new Constraints\NotNull()]
        ]));

        if ($errors->count() > 0) {
            return new ValidationErrorResponse($errors);
        }

        $user = $this->findUserEntity($data['member']);
        if ($project->getUserRole($user)->equals(Role::owner())) {
            throw $this->createAccessDeniedException();
        }

        $project->removeUser($user, $user);
        $this->projectRepository->save($project);

        return new SuccessResponse();
    }

    /**
     * @param string $projectId
     * @return Project
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    private function loadProjectForManage(string $projectId)
    {
        try {
            $project = $this->projectRepository->load(ProjectId::fromString($projectId));
        } catch (ProjectNotFoundException | InvalidProjectIdException $exception) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUser();
        if (!$project->canManageUsers($user)) {
            throw $this->createAccessDeniedException();
        }

        return $project;
    }

    /**
     * @param string[] $usernames
     * @return User[]|object[]
     */
    private function findUserEntities(array $usernames)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        return $userRepository->findBy([
            'username' => $usernames
        ]);
    }

    /**
     * @param string $username
     * @return User|null
     */
    private function findUserEntity(string $username)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        return $userRepository->findOneBy([
            'username' => $username
        ]);
    }
}
