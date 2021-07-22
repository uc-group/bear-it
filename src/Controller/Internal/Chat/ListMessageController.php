<?php

namespace App\Controller\Internal\Chat;

use App\Entity\Chat\Message;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/internal/chat/messages', methods: ['GET'])]
#[Route('/api/chat/messages', methods: ['GET'])]
class ListMessageController extends AbstractController
{
    const LIMIT = 100;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Request $request)
    {
        $room = $request->query->get('room');

        if (!$room) {
            throw $this->createNotFoundException();
        }

        if ($request->query->has('before')) {
            $before = $request->query->get('before');
            $messages = $this->entityManager->getRepository(Message::class)
                ->findBeforeTimestamp($room, $before, self::LIMIT);
        } else {
            $messages = $this->entityManager->getRepository(Message::class)->findBy([
                'roomId' => $room
            ], [
                'postedAt' => 'DESC'
            ], self::LIMIT);
        }

        return new SuccessResponse(array_map(function (Message $message) {
            return $message->toArray();
        }, $messages));
    }
}
