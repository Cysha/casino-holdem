<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Holdem\Cards\SevenCardResultCollection;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Cards\HandCollection;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Holdem\Game\Round;
use Cysha\Casino\Holdem\Game\Table;

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

        $round = Round::start($table);

        $this->assertEquals($seat4, $round->whosTurnIsIt());
        $round->postSmallBlind($seat4);
        $this->assertEquals($seat1, $round->whosTurnIsIt());
        $round->postBigBlind($seat1);

        $this->assertEquals($seat2, $round->whosTurnIsIt());
    }

    /** @test */
    public function button_moves_after_round_ends()
    {
        $game = $this->createGenericGame(5);
        $evaluator = $this->createMock(SevenCard::class);
        $results = $this->createMock(SevenCardResultCollection::class);
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(5000)), Chips::fromAmount(500));
        $hands = $this->createMock(HandCollection::class);

        $evaluator->method('evaluateHands')
            ->willReturn($results);

        $results->method('map')
            ->willReturn($hands);

        $hands->method('first')
            ->willReturn(Hand::createUsingString('4c 2s', $player));

        $dealer = Dealer::startWork(new Deck(), $evaluator);
        $players = $game->players();

        $table = Table::setUp($dealer, $players);

        $round = Round::start($table);
        $round->end();

        $this->assertEquals($table->locatePlayerWithButton(), $players->get(1));
    }
}
