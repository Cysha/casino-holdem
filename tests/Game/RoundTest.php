<?php

namespace xLink\Tests\Exceptions;

use Ramsey\Uuid\Uuid;
use xLink\Poker\Client;
use xLink\Poker\Game\CashGame;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Game;
use xLink\Poker\Game\Round;
use xLink\Poker\Table;

class RoundTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_start_a_round_on_a_table()
    {
        $game = $this->createGenericGame();

        $round = Round::start($game->tables()->first());

        $this->assertCount(4, $round->players());
    }

    /** @test */
    public function the_button_starts_with_the_first_player()
    {
        $game = $this->createGenericGame();

        $table = $game->tables()->first();
        $round = Round::start($table);

        $this->assertEquals($round->playerWithButton(), $table->players()->first());
    }

    /** @test */
    public function the_second_player_is_the_small_blind()
    {
        $game = $this->createGenericGame();

        $table = $game->tables()->first();
        $round = Round::start($table);

        $player2 = $table->players()->get(1);
        $this->assertEquals($round->playerWithSmallBlind(), $player2);
    }

    /** @test */
    public function the_third_player_is_the_big_blind()
    {
        $game = $this->createGenericGame();

        $table = $game->tables()->first();
        $round = Round::start($table);

        $player3 = $table->players()->get(2);
        $this->assertEquals($round->playerWithBigBlind(), $player3);
    }

    /** @test */
    public function the_small_blind_is_moved_when_the_second_player_sit_out()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();

        $table->sitPlayerOut($table->playersSatDown()->get(1));
        $round = Round::start($table);

        $player3 = $table->playersSatDown()->get(1);
        $this->assertEquals($round->playerWithSmallBlind(), $player3);
    }

    /** @test */
    public function the_big_blind_is_moved_when_the_third_player_sit_out()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();

        $table->sitPlayerOut($table->playersSatDown()->get(2));
        $round = Round::start($table);

        $player3 = $table->playersSatDown()->get(2);
        $this->assertEquals($round->playerWithBigBlind(), $player3);
    }

    /** @test */
    public function the_small_blind_is_moved_to_the_fourth_player_if_player_2_and_3_sit_out()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();

        $table->sitPlayerOut($table->players()->get(1)); // player 2
        $table->sitPlayerOut($table->players()->get(2)); // player 3
        $round = Round::start($table);

        $player = $table->playersSatDown()->get(0);
        $this->assertEquals($round->playerWithSmallBlind(), $player);
    }

    /** @test */
    public function if_there_are_only_2_players_then_the_player_with_button_is_small_blind()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();

        $table->sitPlayerOut($table->players()->get(2)); // player 3
        $table->sitPlayerOut($table->players()->get(3)); // player 4
        $round = Round::start($table);

        $player1 = $table->playersSatDown()->get(0);
        $this->assertEquals($round->playerWithButton(), $player1, 'Button is with the wrong player');
        $this->assertEquals($round->playerWithSmallBlind(), $player1, 'small blind is with the wrong player');

        $player2 = $table->playersSatDown()->get(1);
        $this->assertEquals($round->playerWithBigBlind(), $player2, 'big blind is with the wrong player');
    }

    /**
     * @return Game
     */
    private function createGenericGame(): Game
    {
        $xLink = Client::register('xLink', Chips::fromAmount(5500));
        $jesus = Client::register('jesus', Chips::fromAmount(5500));
        $melk = Client::register('melk', Chips::fromAmount(5500));
        $bob = Client::register('bob', Chips::fromAmount(5500));

        // we got a game
        $game = CashGame::setUp(Uuid::uuid4(), 'Demo Cash Game', Chips::fromAmount(500));

        // register clients to game
        $game->registerPlayer($xLink, Chips::fromAmount(5000));
        $game->registerPlayer($jesus, Chips::fromAmount(5000));
        $game->registerPlayer($melk, Chips::fromAmount(5000));
        $game->registerPlayer($bob, Chips::fromAmount(5000));

        $game->assignPlayersToTables(); // table has max of 9 or 5 players in holdem

        return $game;
    }
}
