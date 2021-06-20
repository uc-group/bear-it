<?php

namespace App\Controller\Api\User;

use App\Http\Response\SuccessResponse;
use App\User\Service\UserFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user/find')]
class FindController extends AbstractController
{
    public function __construct(
        private UserFinderInterface $finder
    ) {}

    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $term = trim($data['term'] ?? '');
        if (strlen($term) === 0) {
            return new SuccessResponse([]);
        }

        $result = [];
        foreach ($this->finder->findByTerm($term) as $user) {
            $result[] = [
                'username' => $user->username(),
                'name' => $user->name() ?? $user->username(),
                'avatar' => $user->avatar()->toString()
            ];
        }

        return new SuccessResponse($result);
    }
}
