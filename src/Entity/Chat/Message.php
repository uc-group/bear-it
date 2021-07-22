<?php

namespace App\Entity\Chat;

use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'bi_chat_message')]
#[ORM\Index(columns: ['room_id'], name: 'room_id_idx')]
class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'text')]
    public string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $author;

    #[ORM\Column(type: 'bigint')]
    private int $postedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private string $roomId;

    public function __construct(
        string $id,
        User $author,
        string $content,
        int $timestamp,
        string $roomId
    ) {
        $this->id = $id;
        $this->author = $author;
        $this->content = $content;
        $this->postedAt = $timestamp;
        $this->roomId = $roomId;
    }

    public function changeRoom(string $roomId)
    {
        $this->roomId = $roomId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author->getId()->toString(),
            'content' => $this->content,
            'postedAt' => $this->postedAt,
            'roomId' => $this->roomId
        ];
    }
}
