<?php

namespace App\Utils\ComponentExporter;

use App\Entity\Chat\Channel;
use App\Entity\Chat\Message;
use App\Entity\Pages\Book;
use App\Entity\Pages\Page;
use App\Project\Model\Project\ProjectDescriptor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChatComponentExporter implements ComponentExporterInterface
{
    private string $exportDir = '';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $tempProjectExportDir
    ) {}

    public static function getComponentName(): string
    {
        return 'chat';
    }

    public function export(ProjectDescriptor $descriptor): void
    {
        $this->exportDir = sprintf(
            '%s/%s/chat',
            rtrim($this->tempProjectExportDir, '/'),
            $descriptor->getProjectId()->toString()
        );

        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->exportDir)) {
            $filesystem->mkdir($this->exportDir, 0700);
        }

        $this->exportChat($descriptor);
    }

    public function cleanUp(): void
    {
        $filesystem = new Filesystem();
        if (!empty($this->exportDir)) {
            $filesystem->remove($this->exportDir);
            $this->exportDir = '';
        }
    }

    private function exportChat(ProjectDescriptor $descriptor)
    {
        /** @var Message[] $messages */
        $messages = $this->entityManager->getRepository(Message::class)->findByProject($descriptor->getProjectId());
        /** @var Channel[] $channelEntities */
        $channelEntities = $this->entityManager->getRepository(Channel::class)->findByProject($descriptor->getProjectId());
        $channels = [];
        $channels[sprintf('chat/%s', $descriptor->getProjectId()->toString())] = 'general';
        foreach ($channelEntities as $channel) {
            $channels[sprintf('%s/%s', $channel->room(), $channel->name)] = $channel->name;
        }

        $groupedMessages = [];
        $authors = [];
        foreach ($messages as $message) {
            $messageData = $message->toArray();
            $roomId = $messageData['roomId'];
            $authorId = $messageData['author'];
            if (!array_key_exists($authorId, $authors[$roomId] ?? [])) {
                $author = $message->getAuthor();
                $authors[$roomId][$authorId] = [
                    'name' => $author->getName(),
                    'username' => $author->getUserIdentifier(),
                    'id' => $author->getId(),
                ];
            }

            unset($messageData['roomId']);
            $groupedMessages[$roomId][] = $messageData;
        }

        foreach ($channels as $roomId => $channelName) {
            $fileName = sprintf('%s.json', sha1($roomId));
            $filePath = $this->exportDir . sprintf('/%s', $fileName);
            file_put_contents($filePath, json_encode([
                'name' => $channelName ?? 'general',
                'authors' => $authors[$roomId] ?? [],
                'messages' => $groupedMessages[$roomId] ?? []
            ], JSON_UNESCAPED_UNICODE));
            $descriptor->addFile($filePath, sprintf('chat/%s', $fileName),'chat/room', $roomId);
        }
    }
}
