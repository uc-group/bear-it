<?php

namespace App\Controller\Api;

use App\Entity\AuthToken as AuthTokenEntity;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth-token')]
class AuthTokenController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $token = $this->entityManager->getRepository(AuthTokenEntity::class)->findOneBy([
            'user' => $user
        ]);

        if (!$token) {
            $token = new AuthTokenEntity($user);
            $this->entityManager->persist($token);
            $this->entityManager->flush();
        }

        return new SuccessResponse($token->getToken());
    }
}
