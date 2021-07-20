<?php

namespace App\Controller\Internal\Chat;

use App\Entity\Chat\Message;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Chat\EditMessage;
use App\RequestValidator\Chat\RemoveMessage;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/internal/chat/remove-message', methods: ['POST'])]
#[RequestValidator(RemoveMessage::class)]
class RemoveMessageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke($data)
    {
        $repository = $this->entityManager->getRepository(Message::class);
        $message = $repository->findOneBy([
            'id' => $data['id'] ?? null,
            'author' => $data['author'] ?? null
        ]);
        if ($message instanceof Message) {
            $this->entityManager->remove($message);
            $this->entityManager->flush();
        }

        return new SuccessResponse();
    }
}
