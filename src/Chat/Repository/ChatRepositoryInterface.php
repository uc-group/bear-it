<?php


namespace App\Chat\Repository;


use App\Chat\Exception\ChannelAlreadyExistsException;
use App\Chat\Model\Channel;

interface ChatRepositoryInterface
{
    /**
     * @throws ChannelAlreadyExistsException
     */
    public function addChannel(Channel $channel): void;

}
