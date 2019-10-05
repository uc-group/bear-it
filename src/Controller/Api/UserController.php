<?php

namespace App\Controller\Api;

use App\Http\Response\SuccessResponse;
use App\User\Model\User\Avatar;
use App\User\Service\UserFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    use ControllerTrait;

    /**
     * @param Request $request
     * @param UserFinderInterface $finder
     * @return SuccessResponse
     * @Route("/find")
     */
    public function find(Request $request, UserFinderInterface $finder)
    {
        $data = json_decode($request->getContent(), true);
        $term = trim($data['term'] ?? '');
        if (strlen($term) === 0) {
            return new SuccessResponse([]);
        }

        $result = [];
        foreach ($finder->findByTerm($term) as $user) {
            $result[] = [
                'username' => $user->username(),
                'name' => $user->name() ?? $user->username(),
                'avatar' => $user->avatar()->toString()
            ];
        }

        return new SuccessResponse($result);
    }
}
