<?php

namespace App\Controller\Internal\Chat;

use App\Chat\Exception\ChannelAlreadyExistsException;
use App\Chat\Model\Channel;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Chat\Channel\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/internal/chat/channel/create', name: 'api_chat_channel_create')]
#[RequestValidator(Create::class)]
class CreateChannelController extends AbstractController
{
    public function __construct(
        private ChatRepositoryInterface $chatRepository
    ) {}

    public function __invoke(Channel $channel)
    {
        try {
            $this->chatRepository->addChannel($channel);
        } catch (ChannelAlreadyExistsException $exception) {
            return new JsonResponse([
                'status' => 'ERROR_VALIDATION',
                'errors' => [
                    '[name]' => 'This channel already exists in the room'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        return new SuccessResponse([
            'name' => $channel->name(),
            'room' => $channel->room()
        ]);
    }
}
