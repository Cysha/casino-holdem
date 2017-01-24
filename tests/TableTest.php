<?php

namespace xLink\Tests;

use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Dealer;
use xLink\Poker\Game\Player;
use xLink\Poker\Game\PlayerCollection;
use xLink\Poker\Table;

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
