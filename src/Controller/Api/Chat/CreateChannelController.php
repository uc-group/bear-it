<?php

namespace App\Controller\Api\Chat;

use App\Entity\Chat\Channel;
use App\Http\Response\SuccessResponse;
use App\Http\Response\ValidationErrorResponse;
use App\RequestValidator\Chat\Channel\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/chat/channel/create', name: 'api_chat_channel_create')]
#[RequestValidator(Create::class)]
class CreateChannelController extends AbstractController
{
    public function __invoke(Channel $channel)
    {
        $repository = $this->getDoctrine()->getRepository(Channel::class);
        $existingChannel = $repository->findOneBy([
            'room' => $channel->room(),
            'name' => $channel->name
        ]);

        if ($existingChannel) {
            return new JsonResponse([
                'status' => 'ERROR_VALIDATION',
                'errors' => [
                    '[name]' => 'This channel already exists in the room'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($channel);
        $manager->flush();

        return new SuccessResponse($channel->toArray());
    }
}
