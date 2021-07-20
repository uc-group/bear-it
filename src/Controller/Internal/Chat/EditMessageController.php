<?php

namespace App\Controller\Internal\Chat;

use App\Entity\Chat\Message;
use App\Entity\User;
use App\Http\Response\SuccessResponse;
use App\RequestValidator\Chat\EditMessage;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/internal/chat/message', methods: ['PUT'])]
#[RequestValidator(EditMessage::class)]
class EditMessageController extends AbstractController
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
            $message->content = $data['content'];
            $this->entityManager->flush();

            return new SuccessResponse($message->toArray());
        }

        return new SuccessResponse();
    }
}
