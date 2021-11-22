<?php

namespace App\Entity\Chat;

use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
#[ORM\Table(name: 'bi_chat_channel')]
class Channel
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $roomId;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name;

    public function __construct(string $id, string $roomId, string $name)
    {
        $this->id = $id;
        $this->roomId = $roomId;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'roomId' => $this->roomId,
            'name' => $this->name
        ];
    }

    public function room(): string
    {
        return $this->roomId;
    }
}
