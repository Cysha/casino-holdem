<?php

namespace Cysha\Casino\Holdem\Game\Tests;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Player;
use Cysha\Casino\Holdem\Game\Table;
use Ramsey\Uuid\Uuid;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function can_read_table_details()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $xLink = Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(10000));
        $jesus = Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(10000));
        $melk = Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(10000));
        $bob = Client::register(Uuid::uuid4(), 'bob', Chips::fromAmount(10000));

        $players = PlayerCollection::make([]);
        $players->push(Player::fromClient($xLink, Chips::fromAmount(0)));
        $players->push(Player::fromClient($jesus, Chips::fromAmount(0)));
        $players->push(Player::fromClient($melk, Chips::fromAmount(0)));
        $players->push(Player::fromClient($bob, Chips::fromAmount(0)));

        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $this->assertInstanceOf(Table::class, $table);
        $this->assertInstanceOf(Uuid::class, $table->id());
        $this->assertInstanceOf(PlayerCollection::class, $table->players());
        $this->assertCount(4, $table->players());
        $this->assertInstanceOf(Dealer::class, $table->dealer());
    }

    /** @test **/
    public function can_find_player_by_name()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $xLink = Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(10000));
        $jesus = Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(10000));
        $melk = Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(10000));
        $bob = Client::register(Uuid::uuid4(), 'bob', Chips::fromAmount(10000));

        $players = PlayerCollection::make([]);
        $players->push(Player::fromClient($xLink, Chips::fromAmount(0)));
        $players->push(Player::fromClient($jesus, Chips::fromAmount(0)));
        $players->push(Player::fromClient($melk, Chips::fromAmount(0)));
        $players->push(Player::fromClient($bob, Chips::fromAmount(0)));

        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $this->assertInstanceOf(Player::class, $table->findPlayerByName('xLink'));
        $this->assertEquals($table->playersSatDown()->get(0), $table->findPlayerByName('xLink'));
    }

    /** @test */
    public function can_remove_player_from_table()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $xLink = Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(10000));
        $jesus = Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(10000));
        $melk = Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(10000));
        $bob = Client::register(Uuid::uuid4(), 'bob', Chips::fromAmount(10000));

        $players = PlayerCollection::make([]);
        $players->push(Player::fromClient($xLink, Chips::fromAmount(0)));
        $players->push(Player::fromClient($jesus, Chips::fromAmount(0)));
        $players->push(Player::fromClient($melk, Chips::fromAmount(0)));
        $players->push(Player::fromClient($bob, Chips::fromAmount(0)));

        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $table->removePlayer($xLink);
        $this->assertEquals(3, $table->players()->count());
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\TableException
     * @test
     */
    public function cant_remove_player_from_table_id_hes_not_registered()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $xLink = Client::register(Uuid::uuid4(), 'xLink', Chips::fromAmount(10000));
        $jesus = Client::register(Uuid::uuid4(), 'jesus', Chips::fromAmount(10000));
        $melk = Client::register(Uuid::uuid4(), 'melk', Chips::fromAmount(10000));
        $bob = Client::register(Uuid::uuid4(), 'bob', Chips::fromAmount(10000));
        $blackburn = Client::register(Uuid::uuid4(), 'blackburn', Chips::fromAmount(10000));

        $players = PlayerCollection::make([]);
        $players->push(Player::fromClient($xLink, Chips::fromAmount(0)));
        $players->push(Player::fromClient($jesus, Chips::fromAmount(0)));
        $players->push(Player::fromClient($melk, Chips::fromAmount(0)));
        $players->push(Player::fromClient($bob, Chips::fromAmount(0)));

        $table = Table::setUp(Uuid::uuid4(), $dealer, $players);

        $table->removePlayer($blackburn);
    }

}
