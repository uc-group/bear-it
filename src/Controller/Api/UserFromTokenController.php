<?php

namespace App\Controller\Api;

use App\Entity\AuthToken as AuthTokenEntity;
use App\Http\Response\SuccessResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// TODO: add system auth and remove role public
#[Route('/api/user-from-token')]
class UserFromTokenController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Request $request)
    {
        if (!$request->query->has('token')) {
            throw $this->createNotFoundException();
        }

        /** @var AuthTokenEntity $token */
        $token = $this->entityManager->getRepository(AuthTokenEntity::class)->findOneBy([
            'token' => $request->query->get('token')
        ]);

        return new SuccessResponse($token ? [
            'username' => $token->getUser()->getUserIdentifier(),
            'id' => $token->getUser()->getId()->toString()
        ] : null);
    }
}
