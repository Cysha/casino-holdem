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
use Cysha\Casino\Holdem\Game\Parameters\CashGameParameters;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Holdem\Game\Round;
use Cysha\Casino\Holdem\Game\Table;
use Ramsey\Uuid\Uuid;

class PotBuildingTest extends BaseGameTestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function split_pot_with_3_players()
    {
        $players = PlayerCollection::make([
            Player::fromClient(Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(800)), Chips::fromAmount(800)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(300)), Chips::fromAmount(300)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(150)), Chips::fromAmount(150)),
        ]);
        $xLink = $players->first();
        $jesus = $players->get(1);
        $melk = $players->get(2);

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');
        $winningHand = Hand::fromString('As Ad', $xLink);

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
        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $gameRules = new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start(Uuid::uuid4(), $table, $gameRules);

        $round->postSmallBlind($jesus); // 25
        $round->postBigBlind($melk); // 50

        $round->playerPushesAllIn($xLink); // 150
        $round->playerPushesAllIn($jesus); // SB + 275  (300)

        $round->playerPushesAllIn($melk); // 800 (300)

        $this->assertEquals(800, $round->betStacks()->findByPlayer($xLink)->amount());
        $this->assertEquals(300, $round->betStacks()->findByPlayer($jesus)->amount());
        $this->assertEquals(150, $round->betStacks()->findByPlayer($melk)->amount());

        $round->end();

        /*
        xLink: 800, Jesus: 300, Melk: 150,

        Pot1: (melk smallest...) melk -150, jesus -150, xlink -150 = 450
        xLink: 650, Jesus: 150, Melk: 0

        Pot2: (jesus smallest...)  jesus -150, xlink -150 = 300
        xLink: 500, Jesus: 0

        Pot3: xLink w/ 500
         */
        $this->assertEquals(450, $round->chipPots()->get(0)->total()->amount());
        $this->assertEquals(300, $round->chipPots()->get(1)->total()->amount());
        $this->assertEquals(500, $round->chipPots()->get(2)->total()->amount());
    }

    /** @test */
    public function split_pot_with_left_over_chips()
    {
        $players = PlayerCollection::make([
            Player::fromClient(Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(650)), Chips::fromAmount(2000)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(800)), Chips::fromAmount(300)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(1200)), Chips::fromAmount(800)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'bob', Chips::fromAmount(1200)), Chips::fromAmount(150)),
            Player::fromClient(Client::register(Uuid::uuid4(), 'blackburn', Chips::fromAmount(1200)), Chips::fromAmount(5000)),
        ]);
        $xLink = $players->get(0);
        $jesus = $players->get(1);
        $melk = $players->get(2);
        $bob = $players->get(3);
        $blackburn = $players->get(4);

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');
        $winningHand = Hand::fromString('As Ad', $xLink);

        /** @var SevenCard $evaluator */
        $evaluator = $this->createMock(SevenCard::class);
        $evaluator
            ->method('evaluateHands')
            ->with($this->anything(), $this->anything())
            ->will($this->returnValue(SevenCardResultCollection::make([
                SevenCardResult::createTwoPair($board->merge($winningHand->cards()), $winningHand),
            ])))
        ;

        // Do game
        $dealer = Dealer::startWork(new Deck(), $evaluator);
        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $gameRules = new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(50), null, 9, Chips::fromAmount(500));

        $round = Round::start(Uuid::uuid4(), $table, $gameRules);

        $round->postSmallBlind($jesus); // 25
        $round->postBigBlind($melk); // 50

        $round->playerPushesAllIn($bob); // 150
        $round->playerFoldsHand($blackburn); // 0
        $round->playerPushesAllIn($xLink); // 2000 (300)
        $round->playerPushesAllIn($jesus); // SB + 275
        $round->playerFoldsHand($melk); // 0

        $this->assertEquals(2000, $round->betStacks()->findByPlayer($xLink)->amount());
        $this->assertEquals(300, $round->betStacks()->findByPlayer($jesus)->amount());
        $this->assertEquals(50, $round->betStacks()->findByPlayer($melk)->amount());
        $this->assertEquals(150, $round->betStacks()->findByPlayer($bob)->amount());
        $this->assertEquals(0, $round->betStacks()->findByPlayer($blackburn)->amount());

        $round->end();

        /*
        xLink: 2000, Jesus: 300, Melk: 800, BOB: 150

        Pot1: (bob smallest...) melk -50, bob -150, jesus -150, xlink -150 = 500
        xLink: 1850, Jesus: 150, BOB: 0

        Pot2: (jesus smallest...) jesus -150, xlink -150 = 300
        xLink: 1700, Jesus: 0

        Pot3: xLink w/ 1700

         */

        $this->assertEquals(500, $round->chipPots()->get(0)->total()->amount());
        $this->assertEquals(300, $round->chipPots()->get(1)->total()->amount());
        $this->assertEquals(1700, $round->chipPots()->get(2)->total()->amount());
    }
}
