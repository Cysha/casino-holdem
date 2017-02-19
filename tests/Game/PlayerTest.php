<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Player;

class PlayerTest extends BaseGameTestCase
{
    /** @test */
    public function it_can_be_created_from_a_client()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $this->assertInstanceOf(Client::class, $player);
        $this->assertEquals($client->name(), $player->name());
    }

    /** @test */
    public function players_bet_gets_subtracted_from_chipstack()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client, Chips::fromAmount(5000));

        $this->assertEquals(5000, $player->chipStack()->amount());

        $player->bet(Chips::fromAmount(500));

        $this->assertEquals(4500, $player->chipStack()->amount());
    }

    /**
     * @expectedException Assert\InvalidArgumentException
     * @test
     */
    public function player_cannot_bet_minus_figures()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client, Chips::fromAmount(5000));

        $this->assertEquals(5000, $player->chipStack()->amount());

        $player->bet(Chips::fromAmount(-50));
    }
}
