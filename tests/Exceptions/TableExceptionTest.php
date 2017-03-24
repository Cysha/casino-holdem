<?php

namespace Cysha\Casino\Holdem\Tests\Exceptions;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Exceptions\TableException;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Table;
use PHPUnit_Framework_TestCase as PHPUnit;
use Ramsey\Uuid\Uuid;

class TableExceptionTest extends PHPUnit
{
    /** @test */
    public function invalid_button_position_can_accept_custom_messages()
    {
        $expectedException = new TableException('custom message');
        $this->assertEquals($expectedException, TableException::invalidButtonPosition('custom message'));
    }

    /** @test */
    public function not_registered_can_accept_custom_messages()
    {
        $player = Client::register(Uuid::uuid4(), 'player1', Chips::fromAmount(5500));
        $table = Table::setUp(Uuid::uuid4(), Dealer::startWork(new Deck(), new SevenCard()), PlayerCollection::make([
            $player,
        ]));
        $expectedException = new TableException('custom message');
        $this->assertEquals($expectedException, TableException::notRegistered($player, $table, 'custom message'));
    }
}
