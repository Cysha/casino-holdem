<?php

namespace xLink\Tests\Game;

use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Game;
use xLink\Poker\Game\LeftToAct;
use xLink\Poker\Game\Player;
use xLink\Poker\Game\PlayerCollection;

class LeftToActNewTest extends BaseGameTestCase
{
    /** @test */
    public function test_true()
    {
        $this->assertTrue(true);
    }

    /** @tes6t */
    public function can_create_collection_with_player_collection()
    {
        $game = $this->createMock(Game::class);

        $players = PlayerCollection::make();
        for ($i = 0; $i < 4; ++$i) {
            $players->push(Player::fromClient(Client::register('player'.($i + 1), Chips::fromAmount(5500)), Chips::fromAmount(500)));
        }

        $game->method('players')
                  ->will($this->returnValue($players));

        $leftToAct = LeftToAct::make()->setup($players);

        $expected = LeftToAct::make([
            Seat::fillWithPlayer(0, $players->get(0)),
            Seat::fillWithPlayer(0, $players->get(1)),
            Seat::fillWithPlayer(0, $players->get(2)),
            Seat::fillWithPlayer(0, $players->get(3)),
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }
}
