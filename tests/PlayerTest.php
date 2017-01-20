<?php

namespace xLink\Tests;

use xLink\Poker\Player;

class PlayerTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function a_new_player_can_register()
    {
        $player = Player::register('xLink');
        $this->assertInstanceOf(Player::class, $player);
    }

    /** @test */
    public function i_can_read_the_player_name()
    {
        $player = Player::register('xLink');
        $this->assertEquals('xLink', $player->name());
    }

    /** @test */
    public function it_returns_the_player_name_when_forced_to_string()
    {
        $player = Player::register('xLink');
        $this->assertEquals('xLink', $player->__toString());
    }
}
