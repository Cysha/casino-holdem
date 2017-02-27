<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Holdem\Game\Action;
use Cysha\Casino\Holdem\Game\Player;

class ActionTest extends BaseGameTestCase
{
    /** @test */
    public function can_get_player_from_action()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, Chips::fromAmount(250));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals(Action::CALL, $action->action());
        $this->assertInstanceOf(Player::class, $action->player());
    }

    /** @test */
    public function can_get_chips_from_action()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, Chips::fromAmount(250));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals(250, $action->chips()->amount());
    }

    /** @test */
    public function can_create_action_for_check()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CHECK);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has checked.', $action->toString());
        $this->assertEquals('xLink has checked.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_call()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, Chips::fromAmount(250));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has called 250.', $action->toString());
        $this->assertEquals('xLink has called 250.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_raise()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::RAISE, Chips::fromAmount(999));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has raised 999.', $action->toString());
        $this->assertEquals('xLink has raised 999.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_fold()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::FOLD);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has folded.', $action->toString());
        $this->assertEquals('xLink has folded.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_allin()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::ALLIN, Chips::fromAmount(2453));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has pushed ALL IN (2453).', $action->toString());
        $this->assertEquals('xLink has pushed ALL IN (2453).', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_sb()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::SMALL_BLIND, Chips::fromAmount(25));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has posted Small Blind (25).', $action->toString());
        $this->assertEquals('xLink has posted Small Blind (25).', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_bb()
    {
        $client = Client::register('xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::BIG_BLIND, Chips::fromAmount(50));
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has posted Big Blind (50).', $action->toString());
        $this->assertEquals('xLink has posted Big Blind (50).', $action->__toString());
    }
}
