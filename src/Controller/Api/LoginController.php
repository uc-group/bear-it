<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/login')]
class LoginController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse([
            'authenticated' => $user instanceof User,
            'userData' => $user instanceof User ? [
                'login' => $user->getUserIdentifier(),
                'avatar' => $user->getAvatar(),
                'name' => $user->getName(),
                'id' => $user->getId()->toString()
            ] : null
        ]);
    }
}
