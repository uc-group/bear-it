<?php

namespace App\Controller\Api\Chat;

use App\Chat\Exception\ChannelAlreadyExistsException;
use App\Chat\Model\Channel;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Chat\Channel\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/chat/channel/create', name: 'api_chat_channel_create')]
#[RequestValidator(Create::class)]
class CreateChannelController extends AbstractController
{
    public function __construct(
        private ChatRepositoryInterface $chatRepository,
        private HttpClientInterface $wsClient
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

        $response = $this->wsClient->request('POST', '/notify-room', [
            'json' => [
                'room' => $channel->room(),
                'event' => 'channel-created',
                'message' => $channel->name()
            ]
        ]);

        return new SuccessResponse([
            'name' => $channel->name(),
            'room' => $channel->room(),
            'res' => $response->getHeaders()
        ]);
    }
}
