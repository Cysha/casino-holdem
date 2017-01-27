<?php

namespace xLink\Tests\Exceptions;

use Ramsey\Uuid\Uuid;
use xLink\Poker\Client;
use xLink\Poker\Game\CashGame;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Game;
use xLink\Poker\Game\Player;
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

        $this->assertEquals($round->playerWithButton(), $table->players()->first());
        $player2 = $table->players()->get(1);
        $this->assertEquals($round->playerWithSmallBlind(), $player2);
    }

    /** @test */
    public function the_third_player_is_the_big_blind()
    {
        $game = $this->createGenericGame();

        $table = $game->tables()->first();
        $round = Round::start($table);

        $this->assertEquals($round->playerWithButton(), $table->players()->first());
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

        $this->assertEquals($round->playerWithButton(), $table->players()->first());
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

        $this->assertEquals($round->playerWithButton(), $table->players()->first());
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

    /** @test */
    public function button_will_start_on_first_sat_down_player()
    {
        $xLink = Client::register('xLink', Chips::fromAmount(5500));
        $jesus = Client::register('jesus', Chips::fromAmount(5500));
        $melk = Client::register('melk', Chips::fromAmount(5500));
        $bob = Client::register('bob', Chips::fromAmount(5500));
        $blackburn = Client::register('blackburn', Chips::fromAmount(5500));

        // we got a game
        $game = CashGame::setUp(Uuid::uuid4(), 'Demo Cash Game', Chips::fromAmount(500));

        // register clients to game
        $game->registerPlayer($xLink, Chips::fromAmount(5000)); // x
        $game->registerPlayer($jesus, Chips::fromAmount(5000)); //
        $game->registerPlayer($melk, Chips::fromAmount(5000)); // x
        $game->registerPlayer($bob, Chips::fromAmount(5000)); //
        $game->registerPlayer($blackburn, Chips::fromAmount(5000)); //

        $game->assignPlayersToTables(); // table has max of 9 or 5 players in holdem

        /** @var Table $table */
        $table = $game->tables()->first();
        $table->sitPlayerOut($table->players()->get(0)); // player 1
        $table->sitPlayerOut($table->players()->get(2)); // player 3

        $round = Round::start($table);

        $player2 = $table->players()->get(1);
        $this->assertEquals($round->playerWithButton(), $player2, 'Button is with the wrong player');
        $player4 = $table->players()->get(3);
        $this->assertEquals($round->playerWithSmallBlind(), $player4, 'small blind is with the wrong player');

        $player5 = $table->players()->get(4);
        $this->assertEquals($round->playerWithBigBlind(), $player5, 'big blind is with the wrong player');
    }

    /** @test */
    public function small_blind_from_player_gets_posted_and_added_to_the_pot()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();
        $player1 = $table->playersSatDown()->get(0);
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);

        $round = Round::start($table);
        /*
        [
            xLink: 0, // button
            jesus: 25, // SB
            melk: 50, // BB
            bob: 0,
        ]
        */

        $round->postSmallBlind($player2);
        $this->assertEquals(Chips::fromAmount(25), $round->playerChipCount($player2));

        $round->postBigBlind($player3);
        $this->assertEquals(Chips::fromAmount(50), $round->playerChipCount($player3));
    }

    /** @test */
    public function on_round_start_deal_hands()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();
        $player1 = $table->playersSatDown()->get(0);
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);
        $player4 = $table->playersSatDown()->get(3);

        $round = Round::start($table);

        $round->dealHands();

        $this->assertCount(2, $round->playerHand($player1));
        $this->assertCount(2, $round->playerHand($player2));
        $this->assertCount(2, $round->playerHand($player3));
        $this->assertCount(2, $round->playerHand($player4));
    }

    /**
     * @expectedException xLink\Poker\Exceptions\RoundException
     * @test
     */
    public function on_round_start_stood_up_players_dont_get_dealt_a_hand()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();
        $player4 = $table->playersSatDown()->get(3);

        $table->sitPlayerOut($player4);

        $round = Round::start($table);

        $round->dealHands();

        // This should throw an exception
        $round->playerHand($player4);
    }

    /** @test */
    public function fourth_player_in_proceedings_is_prompted_to_action_after()
    {
        $game = $this->createGenericGame();

        /** @var Table $table */
        $table = $game->tables()->first();
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);
        $player4 = $table->playersSatDown()->get(3);

        $round = Round::start($table);

        $round->postSmallBlind($player2);
        $round->postBigBlind($player3);

        $this->assertEquals($player4, $round->whosTurnIsIt());
    }

    /** @test */
    public function fifth_player_in_proceedings_is_prompted_to_action_after_round_start_when_player_4_is_stood_up()
    {
        $game = $this->createGenericGame(5);

        /** @var Table $table */
        $table = $game->tables()->first();
        $player1 = $table->playersSatDown()->first(); // Button
        $player2 = $table->playersSatDown()->get(1); // SB
        $player3 = $table->playersSatDown()->get(2); // BB
        $player4 = $table->playersSatDown()->get(3); // x [Sat out]
        $player5 = $table->playersSatDown()->get(4); // [turn]

        $round = Round::start($table);

        $round->table()->sitPlayerOut($player4);

        $round->postSmallBlind($player2);
        $round->postBigBlind($player3);

        $this->assertEquals($player5, $round->whosTurnIsIt());
    }

    /** @test */
    public function fourth_player_calls_the_hand_after_blinds_are_posted()
    {
        $game = $this->createGenericGame(5);

        /** @var Table $table */
        $table = $game->tables()->first();
        /** @var Player $player1 */
        $player1 = $table->playersSatDown()->first(); // Button
        $player2 = $table->playersSatDown()->get(1); // SB
        $player4 = $table->playersSatDown()->get(3); // x [Sat out]

        $round = Round::start($table);

        $round->postSmallBlind($player1);
        $round->postBigBlind($player2);

        $round->playerCalls($player4);

        $this->assertEquals(50, $round->playerChipCount($player4)->amount());
        $this->assertEquals(950, $player4->chipStack()->amount());
        $this->assertEquals(950, $round->players()->get(3)->chipStack()->amount());
        $this->assertEquals(125, $round->chipStacks()->total()->amount());
    }

    /** @test */
    public function player_pushes_all_in()
    {
        $game = $this->createGenericGame(5);

        /** @var Table $table */
        $table = $game->tables()->first();
        /** @var Player $player1 */
        $player1 = $table->playersSatDown()->first();
        $player2 = $table->playersSatDown()->get(1);
        $player3 = $table->playersSatDown()->get(2);
        $player4 = $table->playersSatDown()->get(3);

        $round = Round::start($table);

        $round->postSmallBlind($player1); // 25
        $round->postBigBlind($player2); // 50

        $round->playerCalls($player3); // 50
        $round->playerPushesAllIn($player4); // 1000

        $this->assertEquals(1000, $round->playerChipCount($player4)->amount());
        $this->assertEquals(0, $player4->chipStack()->amount());
        $this->assertEquals(0, $round->players()->get(3)->chipStack()->amount());
        $this->assertEquals(1125, $round->chipStacks()->total()->amount());
    }

    /**
     * @expectedException xLink\Poker\Exceptions\RoundException
     * @test
     */
    public function fifth_player_tries_to_raise_the_hand_after_blinds_without_enough_chips()
    {
        $game = $this->createGenericGame(5);

        /** @var Table $table */
        $table = $game->tables()->first();

        /** @var Player $player1 */
        $player1 = $table->playersSatDown()->first();
        $player2 = $table->playersSatDown()->get(1);
        $player4 = $table->playersSatDown()->get(3);
        $player5 = $table->playersSatDown()->get(4);

        $round = Round::start($table);

        $round->postSmallBlind($player1);
        $round->postBigBlind($player2);

        $round->playerCalls($player4);
        $round->playerRaises($player5, Chips::fromAmount(100000));
    }

    /**
     * @expectedException xLink\Poker\Exceptions\RoundException
     * @test
     */
    public function random_player_tries_to_fold_their_hand_after_blinds()
    {
        $game = $this->createGenericGame(5);

        /** @var Table $table */
        $table = $game->tables()->first();

        /** @var Player $player1 */
        $player1 = $table->playersSatDown()->first();
        $player2 = $table->playersSatDown()->get(1);
        $randomPlayer = Player::fromClient(Client::register('Random Player', Chips::fromAmount(1)));

        $round = Round::start($table);

        $round->postSmallBlind($player1);
        $round->postBigBlind($player2);
        $round->playerFoldsHand($randomPlayer);
    }

    /** @test */
    public function button_player_folds_their_hand()
    {
        $game = $this->createGenericGame(4);

        /** @var Table $table */
        $table = $game->tables()->first();

        /** @var Player $player1 */
        $player1 = $table->playersSatDown()->first();
        $player2 = $table->playersSatDown()->get(1); // SB - 25
        $player3 = $table->playersSatDown()->get(2); // BB - 50
        $player4 = $table->playersSatDown()->get(3); // Call - 50

        $round = Round::start($table);

        $round->postSmallBlind($player2);
        $round->postBigBlind($player3);

        $round->playerCalls($player4);
        $round->playerFoldsHand($player1);

        $this->assertEquals(125, $round->chipStacks()->total()->amount());
        $this->assertCount(3, $round->playersStillIn());
        $this->assertFalse($round->playerIsStillIn($player1));
    }

    /**
     * @param int $playerCount
     *
     * @return Game
     */
    private function createGenericGame($playerCount = 4): Game
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
