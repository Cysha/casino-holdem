<?php

namespace xLink\Tests;

use xLink\Poker\Client;

class PlayerTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function a_new_player_can_register()
    {
        $player = Client::register('xLink');
        $this->assertInstanceOf(Client::class, $player);
    }

    /** @test */
    public function i_can_read_the_player_name()
    {
        $player = Client::register('xLink');
        $this->assertEquals('xLink', $player->name());
    }

    /** @test */
    public function it_returns_the_player_name_when_forced_to_string()
    {
        $player = Client::register('xLink');
        $this->assertEquals('xLink', $player->__toString());
    }
}
