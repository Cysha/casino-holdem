<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Cards\Results\SevenCardResult;
use Cysha\Casino\Holdem\Cards\SevenCardResultCollection;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Holdem\Game\Round;
use Cysha\Casino\Holdem\Game\Table;

class WinningDistributionTest extends BaseGameTestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function winning_player_get_entire_pot_added_to_chipstack()
    {
        $client1 = Client::register('player1', Chips::fromAmount(5500));
        $client2 = Client::register('player2', Chips::fromAmount(5500));
        $client3 = Client::register('player3', Chips::fromAmount(5500));
        $player1 = Player::fromClient($client1, Chips::fromAmount(5500));
        $player2 = Player::fromClient($client2, Chips::fromAmount(5500));
        $player3 = Player::fromClient($client3, Chips::fromAmount(5500));

        $players = PlayerCollection::make([
            $player1,
            $player2,
            $player3,
        ]);

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');
        $winningHand = Hand::fromString('As Ad', $player1);

        /** @var SevenCard $evaluator */
        $evaluator = $this->createMock(SevenCard::class);
        $evaluator->method('evaluateHands')
                  ->with($this->anything(), $this->anything())
                  ->will($this->returnValue(SevenCardResultCollection::make([
                      SevenCardResult::createTwoPair($board->merge($winningHand->cards()), $winningHand),
                  ])))
        ;

        // Do game
        $dealer = Dealer::startWork(new Deck(), $evaluator);
        $table = Table::setUp($dealer, $players);

        $seat1 = $table->playersSatDown()->get(0);
        $seat2 = $table->playersSatDown()->get(1);
        $seat3 = $table->playersSatDown()->get(2);

        $round = Round::start($table);

        $this->dealHandsAndPlayGame($round, $seat2, $seat3, $seat1);

        $this->assertEquals(150, $round->currentPot()->totalAmount());
        $round->end();

        $this->assertEquals($winningHand->player(), $round->winningPlayer());
        $this->assertEquals(150, $round->chipPots()->get(0)->totalAmount());
        $this->assertEquals(0, $round->currentPot()->totalAmount());
        $this->assertEquals(5600, $round->players()->get(0)->chipStack()->amount());
    }

    /**
     * @param $round
     * @param $seat2
     * @param $seat3
     * @param $seat1
     */
    private function dealHandsAndPlayGame(Round $round, $seat2, $seat3, $seat1)
    {
        $round->dealHands();

        $round->postSmallBlind($seat2);
        $round->postBigBlind($seat3);

        $round->playerCalls($seat1);
        $round->playerCalls($seat2);
        $round->playerChecks($seat3);

        $round->dealFlop();

        $round->playerChecks($seat2);
        $round->playerChecks($seat3);
        $round->playerChecks($seat1);

        $round->dealTurn();

        $round->playerChecks($seat2);
        $round->playerChecks($seat3);
        $round->playerChecks($seat1);

        $round->dealRiver();
    }
}
