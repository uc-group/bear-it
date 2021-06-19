<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private string $clientId;

    public function __construct($clientId)
    {

        $this->clientId = $clientId;
    }

    /**
     * @Route("/", name="homepage")
     * @Route("/{vueRoute}", requirements={"vueRoute": "^(?!api|auth|logout|_).*"})
     */
    public function homepage(Request $request)
    {
        return $this->render('homepage.html.twig', [
            'config' => [
                'githubClientId' => $this->clientId
            ]
        ]);
    }


    /**
     * @Route("/api/login")
     */
    public function login()
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
