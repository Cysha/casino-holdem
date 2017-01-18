<?php

namespace xLink\Tests;

use xLink\Poker\Game\Chips;
use xLink\Poker\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function a_new_client_can_register()
    {
        $client = Client::register('xLink');
        $this->assertInstanceOf(Client::class, $client);
    }

    /** @test */
    public function i_can_read_the_client_name()
    {
        $client = Client::register('xLink');
        $this->assertEquals('xLink', $client->name());
    }

    /** @test */
    public function it_returns_the_client_name_when_forced_to_string()
    {
        $client = Client::register('xLink');
        $this->assertEquals('xLink', $client->__toString());
    }

    /** @test */
    public function client_has_no_chips_in_wallet()
    {
        $client = Client::register('xLink');

        $this->assertInstanceOf(Chips::class, $client->wallet());
        $this->assertEquals(0, $client->wallet()->amount());
    }

    /** @test */
    public function client_has_defined_chips_in_wallet()
    {
        $chipCount = Chips::fromAmount(5000);
        $client = Client::register('xLink', $chipCount);

        $this->assertInstanceOf(Chips::class, $client->wallet());
        $this->assertEquals($chipCount->amount(), $client->wallet()->amount());
    }

    /** @test */
    public function client_chips_can_be_added()
    {
        $chipCount = Chips::fromAmount(5000);
        $client = Client::register('xLink', $chipCount);
        $client->wallet()->add(Chips::fromAmount(100));

        $this->assertEquals(5100, $client->wallet()->amount());
    }

    /** @test */
    public function client_chips_can_be_subtracted()
    {
        $chipCount = Chips::fromAmount(5000);
        $client = Client::register('xLink', $chipCount);
        $client->wallet()->subtract(Chips::fromAmount(100));

        $this->assertEquals(4900, $client->wallet()->amount());
    }
}
