<?php

namespace xLink\Tests\Exceptions;

use xLink\Poker\Client;
use xLink\Poker\Game\Player;

class PlayerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_be_created_from_a_client()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $this->assertInstanceOf(Client::class, $player);
        $this->assertEquals($client->name(), $player->name());
    }
}
