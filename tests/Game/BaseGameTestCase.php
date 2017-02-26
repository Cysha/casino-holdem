<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Contracts\Game;
use Cysha\Casino\Holdem\Game\CashGame;
use Cysha\Casino\Holdem\Game\Parameters\CashGameParameters;
use PHPUnit_Framework_TestCase as PHPUnit;
use Ramsey\Uuid\Uuid;

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

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        // we got a game
        $game = CashGame::setUp(Uuid::uuid4(), 'Demo Cash Game', $gameRules);

        // register clients to game
        foreach ($players as $player) {
            $game->registerPlayer($player, Chips::fromAmount(1000));
        }

        $game->assignPlayersToTables(); // table has max of 9 or 5 players in holdem

        return $game;
    }
}
