<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/users')]
#[RequestValidator(Users::class)]
class UsersController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(array $userIds)
    {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findBy([
            'id' => $userIds
        ]);

        $result = [];
        foreach ($users as $user) {
            $result[$user->getId()->toString()] = [
                'id' => $user->getId()->toString(),
                'avatar' => $user->getAvatar(),
                'name' => $user->getName(),
                'username' => $user->getUserIdentifier()
            ];
        }

        return new SuccessResponse($result);
    }
}
