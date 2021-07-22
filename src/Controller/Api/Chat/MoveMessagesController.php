<?php

namespace App\Controller\Api\Chat;

use App\Chat\Exception\ChannelAlreadyExistsException;
use App\Chat\Model\Channel;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Entity\Chat\Message;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Chat\Channel\Create;
use App\RequestValidator\Chat\MoveMessages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/chat/move-messages', name: 'api_chat_move_messages', methods: ['POST'])]
#[RequestValidator(MoveMessages::class)]
class MoveMessagesController extends AbstractController
{
    public function __invoke(string $room, array $messages)
    {
        /** @var Message[] $entities */
        $entities = $this->getDoctrine()->getRepository(Message::class)->findBy([
            'id' => $messages
        ]);

        foreach ($entities as $entity) {
            $entity->changeRoom($room);
        }

        $this->getDoctrine()->getManager()->flush();

        return new SuccessResponse();
    }
}
