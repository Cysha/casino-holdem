<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\LeftToAct;
use Cysha\Casino\Holdem\Game\Parameters\CashGameParameters;
use Cysha\Casino\Holdem\Game\Round;
use Cysha\Casino\Holdem\Table;

class LeftToActTest extends BaseGameTestCase
{
    /** @test */
    public function can_create_collection_with_player_collection()
    {
        $game = $this->createGenericGame(4);

        $leftToAct = LeftToAct::make([])->setup($game->players());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_create_collection_with_with_dealer_being_last()
    {
        $game = $this->createGenericGame(4);

        $leftToAct = LeftToAct::make([])->setupWithoutDealer($game->players());

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_move_player_to_last_in_queue()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->movePlayerToLastInQueue();

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_reset_player_list_from_seat_number()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->resetPlayerListFromSeat(3);

        $expected = LeftToAct::make([
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function can_set_player_activity()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->setActivity($seat1->name(), LeftToAct::ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ACTIONED],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function player_can_action()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->playerHasActioned($seat1, LeftToAct::ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function player_can_aggressively_action()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->playerHasActioned($seat1, LeftToAct::AGGRESSIVELY_ACTIONED);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function player_can_all_in()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();
        $seat1 = $table->playersSatDown()->get(0);

        $leftToAct = LeftToAct::make([])
            ->setup($table->playersSatDown());

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $leftToAct);

        $leftToAct = $leftToAct->playerHasActioned($seat1, LeftToAct::ALL_IN);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ALL_IN],
        ]);
        $this->assertEquals($expected, $leftToAct);
    }

    /** @test */
    public function checks_leftToAct_throughout_a_complete_round()
    {
        $game = $this->createGenericGame(4);

        $table = $game->tables()->first();

        $player1 = $table->playersSatDown()->get(0);
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);
        $player4 = $table->playersSatDown()->get(3);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // deal some hands
        $round->dealHands();

        $round->postSmallBlind($player2); // 25
        $round->postBigBlind($player3); // 50

        $round->playerCalls($player4); // 50
        $round->playerFoldsHand($player1);
        $round->playerCalls($player2); // SB + 25
        $round->playerChecks($player3); // BB

        $expected = LeftToAct::make([
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::ACTIONED],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // collect the chips, burn a card, deal the flop
        $round->dealFlop();

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerChecks($player2); // 0
        $round->playerRaises($player3, Chips::fromAmount(250)); // 250
        $round->playerCalls($player4); // 250
        $round->playerFoldsHand($player2);

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // collect chips, burn 1, deal 1
        $round->dealTurn();

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerRaises($player3, Chips::fromAmount(450)); // 450
        $round->playerCalls($player4); // 450

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::AGGRESSIVELY_ACTIONED],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        // collect chips, burn 1, deal 1
        $round->dealRiver();

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerPushesAllIn($player3); // 250
        $round->playerCalls($player4); // 250

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::ALL_IN],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ALL_IN],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->end();
    }

    /** @test */
    public function when_the_dealer_starts_the_new_betting_round_with_two_players_the_first_player_to_act_is_the_small_blind()
    {
        $game = $this->createGenericGame(2);

        /** @var Table $table */
        $table = $game->tables()->first();

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $round->playerChecks($seat1);
        $round->playerChecks($seat2);

        // Deal flop
        $round->dealFlop();

        $this->assertEquals($game->players()->get(0), $round->whosTurnIsIt());
    }

    /** @test */
    public function actioned_player_gets_pushed_to_last_place_on_leftToAct_collection()
    {
        $game = $this->createGenericGame(9);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat2 = $table->playersSatDown()->get(1);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 6, 'player' => 'player7', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 7, 'player' => 'player8', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 8, 'player' => 'player9', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerCalls($seat2);

        $expected = LeftToAct::make([
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 6, 'player' => 'player7', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 7, 'player' => 'player8', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 8, 'player' => 'player9', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());
    }

    /** @test */
    public function aggressive_action_resets_all_actions()
    {
        $game = $this->createGenericGame(6);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);
        $seat5 = $table->playersSatDown()->get(4);
        $seat6 = $table->playersSatDown()->get(5);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat2);
        $round->postBigBlind($seat3);

        $round->playerCalls($seat4);
        $round->playerCalls($seat5);
        $round->playerCalls($seat6);
        $round->playerPushesAllIn($seat1);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ALL_IN],
        ]);

        $this->assertEquals($expected, $round->leftToAct());
    }

    /** @test */
    public function fold_action_gets_players_removed_from_leftToAct()
    {
        $game = $this->createGenericGame(6);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);
        $seat4 = $table->playersSatDown()->get(3);
        $seat5 = $table->playersSatDown()->get(4);
        $seat6 = $table->playersSatDown()->get(5);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 4, 'player' => 'player5', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat2);
        $round->postBigBlind($seat3);

        $round->playerCalls($seat4);
        $round->playerFoldsHand($seat5);

        $expected = LeftToAct::make([
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::SMALL_BLIND],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::BIG_BLIND],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::ACTIONED],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->playerCalls($seat6);
        $round->playerPushesAllIn($seat1);

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 2, 'player' => 'player3', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 3, 'player' => 'player4', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 5, 'player' => 'player6', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ALL_IN],
        ]);
        $this->assertEquals($expected, $round->leftToAct());
    }

    /** @test */
    public function player_can_raise_for_all_stack_but_counts_as_allin()
    {
        $game = $this->createGenericGame(2);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat1); // 25
        $round->postBigBlind($seat2); // 50

        $round->playerCalls($seat1); // 50
        $round->playerRaises($seat2, Chips::fromAmount(950));
        $round->playerCalls($seat1); // 950

        $expected = LeftToAct::make([
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::ALL_IN],
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::ALL_IN],
        ]);
        $this->assertEquals($expected, $round->leftToAct());
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\RoundException
     * @test
     */
    public function make_sure_that_raise_is_higher_than_highest_bet_this_mini_round()
    {
        $game = $this->createGenericGame(2);

        /** @var Table $table */
        $table = $game->tables()->first();

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);

        $gameRules = new CashGameParameters(Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start($table, $gameRules);

        $expected = LeftToAct::make([
            ['seat' => 0, 'player' => 'player1', 'action' => LeftToAct::STILL_TO_ACT],
            ['seat' => 1, 'player' => 'player2', 'action' => LeftToAct::STILL_TO_ACT],
        ]);
        $this->assertEquals($expected, $round->leftToAct());

        $round->postSmallBlind($seat1); // 25
        $round->postBigBlind($seat2); // 50

        $round->playerRaises($seat1, Chips::fromAmount(5));
    }
}
