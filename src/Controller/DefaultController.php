<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'homepage')]
#[Route('/{vueRoute}', requirements: ['vueRoute' => '^(?!api|auth|logout|_|internal).*'])]
class DefaultController extends AbstractController
{
    public function __construct(
        private string $githubClientId
    ) {}

    public function __invoke(Request $request): Response
    {
        return $this->render('homepage.html.twig', [
            'config' => [
                'githubClientId' => $this->githubClientId
            ]
        ]);
    }
}
