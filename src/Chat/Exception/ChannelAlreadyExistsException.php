<?php

namespace App\Chat\Exception;


use App\Chat\Model\Channel;

class ChannelAlreadyExistsException extends \Exception
{
    public static function create(Channel $channel)
    {
        return new self(sprintf(
            'Channel "%s" already exists in the room "%s"',
            $channel->name(),
            $channel->room()
        ));
    }
}
