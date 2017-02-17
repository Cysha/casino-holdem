<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Ramsey\Uuid\Uuid;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Holdem\Game\CashGame;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Contracts\Game;
use PHPUnit_Framework_TestCase as PHPUnit;

class BaseGameTestCase extends PHPUnit
{
    /**
     * @param int $playerCount
     *
     * @return Game
     */
    public function createGenericGame($playerCount = 4): Game
    {
        $players = [];
        for ($i = 0; $i < $playerCount; ++$i) {
            $players[] = Client::register('player'.($i + 1), Chips::fromAmount(5500));
        }

        // we got a game
        $game = CashGame::setUp(Uuid::uuid4(), 'Demo Cash Game', Chips::fromAmount(500));

        // register clients to game
        foreach ($players as $player) {
            $game->registerPlayer($player, Chips::fromAmount(1000));
        }

        $game->assignPlayersToTables(); // table has max of 9 or 5 players in holdem

        return $game;
    }
}
