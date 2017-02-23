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

        // $this->assertEquals($winningHand->player(), $round->winningPlayer());
        $this->assertEquals(150, $round->chipPots()->get(0)->totalAmount());
        $this->assertEquals(5600, $round->players()->get(0)->chipStack()->amount());
    }

    /*
        5 Player Hand:
            xLink 2000 chips
            jesus 300 chips
            melk 800 chips
            bob 150 chips
            blackburn 5000 chips

        Round plays out like such:
            $round->postSmallBlind($jesus); // 25
            $round->postBigBlind($melk); // 50

            $round->playerPushesAllIn($bob); // 150
            $round->playerFoldsHand($blackburn); // 0
            $round->playerPushesAllIn($xLink); // 2000
            $round->playerPushesAllIn($jesus); // SB + 275
            $round->playerFoldsHand($melk); // 50

        No more player action left:
            Flop, Turn && River get dealt out

        Pots have following amounts in
            1: 500   [xLink, jesus, bob, [melk]]
            2: 300   [xLink, jesus, bob]
            3: 1700  [xLink]

    Winning Scenarios::::
        if:
            - xLink wins, wins EVERYTHING

        if:
            - Jesus wins, wins everything from pot 1, 2
            - pot 3 still goes to xLink

        if:
            - bob wins, wins pot 1 and 2
            - pot 3 still goes to xLink

    */

    /** @test */
    public function scenario_1()
    {
        $players = PlayerCollection::make([
            Player::fromClient(Client::register('xLink', Chips::fromAmount(650)), Chips::fromAmount(2000)),
            Player::fromClient(Client::register('jesus', Chips::fromAmount(800)), Chips::fromAmount(300)),
            Player::fromClient(Client::register('melk', Chips::fromAmount(1200)), Chips::fromAmount(800)),
            Player::fromClient(Client::register('bob', Chips::fromAmount(1200)), Chips::fromAmount(150)),
            Player::fromClient(Client::register('blackburn', Chips::fromAmount(1200)), Chips::fromAmount(5000)),
        ]);
        $xLink = $players->get(0);
        $jesus = $players->get(1);
        $melk = $players->get(2);
        $bob = $players->get(3);
        $blackburn = $players->get(4);

        $winningHand = Hand::fromString('As Ad', $xLink);

        $round = $this->dealGameForSplitPot($players, $winningHand);

        $this->assertEquals(2500, $round->players()->get(0)->chipStack()->amount());
        $this->assertEquals(0, $round->players()->get(1)->chipStack()->amount());
        $this->assertEquals(750, $round->players()->get(2)->chipStack()->amount());
        $this->assertEquals(0, $round->players()->get(3)->chipStack()->amount());
        $this->assertEquals(5000, $round->players()->get(4)->chipStack()->amount());
    }

    /** @test */
    public function scenario_2()
    {
        $players = PlayerCollection::make([
            Player::fromClient(Client::register('xLink', Chips::fromAmount(650)), Chips::fromAmount(2000)),
            Player::fromClient(Client::register('jesus', Chips::fromAmount(800)), Chips::fromAmount(300)),
            Player::fromClient(Client::register('melk', Chips::fromAmount(1200)), Chips::fromAmount(800)),
            Player::fromClient(Client::register('bob', Chips::fromAmount(1200)), Chips::fromAmount(150)),
            Player::fromClient(Client::register('blackburn', Chips::fromAmount(1200)), Chips::fromAmount(5000)),
        ]);
        $xLink = $players->get(0);
        $jesus = $players->get(1);
        $melk = $players->get(2);
        $bob = $players->get(3);
        $blackburn = $players->get(4);

        $winningHand = Hand::fromString('As Ad', $jesus);

        $round = $this->dealGameForSplitPot($players, $winningHand);

        $this->assertEquals(1700, $round->players()->get(0)->chipStack()->amount());
        $this->assertEquals(800, $round->players()->get(1)->chipStack()->amount());
        $this->assertEquals(750, $round->players()->get(2)->chipStack()->amount());
        $this->assertEquals(0, $round->players()->get(3)->chipStack()->amount());
        $this->assertEquals(5000, $round->players()->get(4)->chipStack()->amount());
    }

    /** @test */
    public function scenario_3()
    {
        $players = PlayerCollection::make([
            Player::fromClient(Client::register('xLink', Chips::fromAmount(650)), Chips::fromAmount(2000)),
            Player::fromClient(Client::register('jesus', Chips::fromAmount(800)), Chips::fromAmount(300)),
            Player::fromClient(Client::register('melk', Chips::fromAmount(1200)), Chips::fromAmount(800)),
            Player::fromClient(Client::register('bob', Chips::fromAmount(1200)), Chips::fromAmount(150)),
            Player::fromClient(Client::register('blackburn', Chips::fromAmount(1200)), Chips::fromAmount(5000)),
        ]);
        $xLink = $players->get(0);
        $jesus = $players->get(1);
        $melk = $players->get(2);
        $bob = $players->get(3);
        $blackburn = $players->get(4);

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');
        $bobHand = Hand::fromString('As Ad', $bob);
        $xLinkHand = Hand::fromString('2c 5d', $xLink);
        $jesusHand = Hand::fromString('Ts Td', $jesus);

        $sevenCardMock = $this->createMock(SevenCard::class);
        $sevenCardMock->expects($this->at(0))
            ->method('evaluateHands')
            ->with($this->anything(), $this->anything())
            ->will($this->returnValue(SevenCardResultCollection::make([
                SevenCardResult::createTwoPair($board->merge($jesusHand->cards()), $jesusHand),
            ])));

        $sevenCardMock->expects($this->at(1))
           ->method('evaluateHands')
           ->with($this->anything(), $this->anything())
           ->will($this->returnValue(SevenCardResultCollection::make([
               SevenCardResult::createTwoPair($board->merge($bobHand->cards()), $bobHand),
           ])));

        // Do game
        $dealer = Dealer::startWork(new Deck(), $sevenCardMock);
        $table = Table::setUp($dealer, $players);

        $round = Round::start($table);

        // Round start

        $round->postSmallBlind($jesus); // 25
        $round->postBigBlind($melk); // 50

        $round->playerPushesAllIn($bob); // 150
        $round->playerFoldsHand($blackburn); // 0
        $round->playerPushesAllIn($xLink); // 2000 (300)
        $round->playerPushesAllIn($jesus); // SB + 275
        $round->playerFoldsHand($melk); // 0

        $round->end();

        /*
            xLink: 2000, Jesus: 300, Melk: 800, BOB: 150

            Pot1: (bob smallest...) melk -50, bob -150, jesus -150, xlink -150 = 500
                xLink: 1850, Jesus: 150, BOB: 0

            Pot2: (jesus smallest...) jesus -150, xlink -150 = 300
                xLink: 1700, Jesus: 0

            Pot4: xLink w/ 1700
        */
        // dump($round->betStacks()->map->__toString());
        // dump($round->chipPots()->map->__toString());

        $this->assertEquals(1700, $round->players()->get(0)->chipStack()->amount());
        $this->assertEquals(300, $round->players()->get(1)->chipStack()->amount());
        $this->assertEquals(750, $round->players()->get(2)->chipStack()->amount());
        $this->assertEquals(500, $round->players()->get(3)->chipStack()->amount());
        $this->assertEquals(5000, $round->players()->get(4)->chipStack()->amount());
    }

    private function dealGameForSplitPot(PlayerCollection $players, Hand $winningHand)
    {
        $xLink = $players->get(0);
        $jesus = $players->get(1);
        $melk = $players->get(2);
        $bob = $players->get(3);
        $blackburn = $players->get(4);

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');

        /** @var SevenCard $evaluator */
        $evaluator = $this->createMock(SevenCard::class);
        $evaluator->method('evaluateHands')
            ->with($this->anything(), $this->anything())
            ->will($this->returnValue(SevenCardResultCollection::make([
                SevenCardResult::createTwoPair($board->merge($winningHand->cards()), $winningHand),
            ])));

        // Do game
        $dealer = Dealer::startWork(new Deck(), $evaluator);
        $table = Table::setUp($dealer, $players);

        $round = Round::start($table);

        // Round start

        $round->postSmallBlind($jesus); // 25
        $round->postBigBlind($melk); // 50

        $round->playerPushesAllIn($bob); // 150
        $round->playerFoldsHand($blackburn); // 0
        $round->playerPushesAllIn($xLink); // 2000 (300)
        $round->playerPushesAllIn($jesus); // SB + 275
        $round->playerFoldsHand($melk); // 0

        // assume xLink won
        $round->end();

        /*
            xLink: 2000, Jesus: 300, Melk: 800, BOB: 150

            Pot1: (bob smallest...) melk -50, bob -150, jesus -150, xlink -150 = 500
                xLink: 1850, Jesus: 150, BOB: 0

            Pot2: (jesus smallest...) jesus -150, xlink -150 = 300
                xLink: 1700, Jesus: 0

            Pot4: xLink w/ 1700
        */
        // dump($round->betStacks()->map->__toString());
        // dump($round->chipPots()->map->__toString());

        return $round;
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
