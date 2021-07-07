<?php

namespace App\Controller\Api\Chat;

use App\Entity\Chat\Channel;
use App\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chat/channel-list/{name}', requirements: ['name' => '^.*'])]
class ListChannelsController extends AbstractController
{
    public function __invoke(string $name)
    {
        $channels = $this->getDoctrine()->getRepository(Channel::class)->findBy([
            'roomId' => $name
        ], [
            'name' => 'ASC'
        ]);

        return new SuccessResponse(array_map(function (Channel $channel) {
            return $channel->toArray();
        }, $channels));
    }
}
