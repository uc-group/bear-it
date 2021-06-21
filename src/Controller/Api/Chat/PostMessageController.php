<?php

namespace App\Controller\Api\Chat;

use App\Entity\Chat\Message;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chat/messages', methods: ['POST'])]
class PostMessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        foreach (($data ?? []) as $row) {
            $message = new Message(
                $row['id'] ?? Uuid::uuid4()->toString(),
                $this->entityManager->getReference(User::class, $row['author']),
                $row['content'],
                $row['date'],
                $row['room']
            );
            $this->entityManager->persist($message);
        }
        $this->entityManager->flush();

        return new SuccessResponse();
    }
}
