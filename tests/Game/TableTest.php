<?php

namespace Cysha\Casino\Holdem\Game\Tests;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Holdem\Game\Table;

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

    /** @test **/
    public function can_find_player_by_name()
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

        $this->assertInstanceOf(Player::class, $table->findPlayerByName('xLink'));
        $this->assertEquals($table->playersSatDown()->get(0), $table->findPlayerByName('xLink'));
    }
}
