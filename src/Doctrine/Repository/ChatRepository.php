<?php

namespace App\Doctrine\Repository;

use App\Chat\Exception\ChannelAlreadyExistsException;
use App\Chat\Model\Channel;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Entity\Chat\Channel as ChannelEntity;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ChatRepository implements ChatRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function addChannel(Channel $channel): void
    {
        $repository = $this->entityManager->getRepository(ChannelEntity::class);
        $existingChannel = $repository->findOneBy([
            'roomId' => $channel->room(),
            'name' => $channel->name()
        ]);

        if ($existingChannel) {
            throw ChannelAlreadyExistsException::create($channel);
        }

        $entity = new ChannelEntity(Uuid::uuid4(), $channel->room(), $channel->name());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
