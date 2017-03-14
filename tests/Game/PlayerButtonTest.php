<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Parameters\CashGameParameters;
use Cysha\Casino\Holdem\Game\Round;
use Cysha\Casino\Holdem\Game\Table;
use Ramsey\Uuid\Uuid;

class PlayerButtonTest extends BaseGameTestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function can_pass_the_dealer_button()
    {
        $game = $this->createGenericGame(4);

        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $this->assertEquals($seat1, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat2, $table->locatePlayerWithButton());
    }

    /** @test */
    public function can_pass_the_button_back_to_starting_player_when_reaching_end_of_table()
    {
        $game = $this->createGenericGame(2);

        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $this->assertEquals($seat1, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat2, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat1, $table->locatePlayerWithButton());
    }

    /** @test */
    public function can_pass_the_button_over_sat_out_players()
    {
        $game = $this->createGenericGame(3);

        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);

        $table->sitPlayerOut($seat2);
        $this->assertEquals($seat1, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat3, $table->locatePlayerWithButton());
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\TableException
     * @test
     */
    public function cant_give_button_to_sat_out_player()
    {
        $game = $this->createGenericGame(3);

        $table = $game->tables()->first();
        $seat2 = $table->playersSatDown()->get(1);

        $table->sitPlayerOut($seat2);

        $table->giveButtonToPlayer($seat2);
    }

    /** @test */
    public function can_give_a_specific_player_the_button()
    {
        $game = $this->createGenericGame(4);

        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);
        // $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);

        $table->giveButtonToPlayer($seat3);
        $this->assertEquals($seat3, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat4, $table->locatePlayerWithButton());

        $table->moveButton();
        $this->assertEquals($seat1, $table->locatePlayerWithButton());
    }

    /** @test */
    public function when_button_gets_given_to_a_player_ensure_blinds_are_followed()
    {
        $game = $this->createGenericGame(4);

        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);

        $table->giveButtonToPlayer($seat3);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start(Uuid::uuid4(), $table, $gameRules);

        $this->assertEquals($seat4, $round->whosTurnIsIt());
        $round->postSmallBlind($seat4);
        $this->assertEquals($seat1, $round->whosTurnIsIt());
        $round->postBigBlind($seat1);

        $this->assertEquals($seat2, $round->whosTurnIsIt());
    }

    /** @test */
    public function button_moves_after_round_ends()
    {
        $game = $this->createGenericGame(2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $table = Table::setUp(Uuid::uuid4(), $dealer, $game->players());
        $seat1 = $table->players()->get(0);
        $seat2 = $table->players()->get(1);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start(Uuid::uuid4(), $table, $gameRules);
        $round->dealHands();
        $round->postSmallBlind($seat1);
        $round->postBigBlind($seat2);

        $round->playerCalls($seat1);
        $round->playerCalls($seat2);

        $round->dealFlop();

        $round->playerChecks($seat1);
        $round->playerChecks($seat2);

        $round->dealTurn();

        $round->playerChecks($seat1);
        $round->playerChecks($seat2);

        $round->dealRiver();

        $round->playerChecks($seat1);
        $round->playerChecks($seat2);

        $round->end();

        $this->assertEquals($table->locatePlayerWithButton(), $seat2);
    }
}
