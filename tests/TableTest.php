<?php

namespace Cysha\Casino\Holdem\Tests;

use Cysha\Casino\Holdem\Cards\Deck;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Client;
use Cysha\Casino\Holdem\Game\Chips;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Holdem\Game\PlayerCollection;
use Cysha\Casino\Holdem\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function can_read_table_details()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $xLink = Client::register('xLink', Chips::fromAmount(10000));
        $jesus = Client::register('jesus', Chips::fromAmount(10000));
        $melk = Client::register('melk', Chips::fromAmount(10000));
        $bob = Client::register('bob', Chips::fromAmount(10000));

        $players = PlayerCollection::make([]);
        $players->push(Player::fromClient($xLink, Chips::fromAmount(0)));
        $players->push(Player::fromClient($jesus, Chips::fromAmount(0)));
        $players->push(Player::fromClient($melk, Chips::fromAmount(0)));
        $players->push(Player::fromClient($bob, Chips::fromAmount(0)));

        $table = Table::setUp($dealer, $players);

        $this->assertInstanceOf(Table::class, $table);
        $this->assertInstanceOf(PlayerCollection::class, $table->players());
        $this->assertCount(4, $table->players());
        $this->assertInstanceOf(Dealer::class, $table->dealer());
    }
}
