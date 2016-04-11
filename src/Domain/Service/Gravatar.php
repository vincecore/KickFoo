<?php

namespace KickFoo\Domain\Service;

use Buzz\Browser;
use KickFoo\Domain\Entity\Player;

class Gravatar
{
    public function importAvatar(Player $player)
    {
        $client = new Browser;
        $response = $client->get('http://www.gravatar.com/avatar/' . md5($player->getEmail()) . '?s=64');
        return $response->getContent();
    }
}
