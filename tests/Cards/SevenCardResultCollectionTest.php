<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Holdem\Cards\Results\SevenCardResult;
use Cysha\Casino\Holdem\Cards\SevenCardResultCollection;
use Cysha\Casino\Holdem\Game\Player;
use Ramsey\Uuid\Uuid;

class SevenCardResultCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function can_create_collection_of_results()
    {
        $client1 = Client::register(Uuid::uuid4(), 'player1', Chips::fromAmount(5500));
        $player1 = Player::fromClient($client1, Chips::fromAmount(5500));

        $board = CardCollection::fromString('3s 3h 8h 2s 4c');
        $winningHand = Hand::fromString('As Ad', $player1);

        $resultCollection = SevenCardResultCollection::make([
            SevenCardResult::createTwoPair($board->merge($winningHand->cards()), $winningHand),
        ]);

        $this->assertInstanceOf(SevenCardResultCollection::class, $resultCollection);
        $this->assertEquals('Two Pair - 3s and As', $resultCollection->__toString());
    }
}
