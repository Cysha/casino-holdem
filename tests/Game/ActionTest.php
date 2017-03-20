<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Cysha\Casino\Holdem\Game\Action;
use Cysha\Casino\Holdem\Game\Dealer;
use Cysha\Casino\Holdem\Game\Player;
use Ramsey\Uuid\Uuid;

class ActionTest extends BaseGameTestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @test
     */
    public function chips_passed_to_chips_attribute_needs_to_be_chip_object()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        new Action($player, Action::CALL, ['chips' => 0]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @test
     */
    public function cards_passed_to_communityCards_attribute_needs_to_be_cardCollection_object()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        new Action($player, Action::CALL, ['communityCards' => 0]);
    }

    /** @test */
    public function can_get_player_from_action()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, ['chips' => Chips::fromAmount(250)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals(Action::CALL, $action->action());
        $this->assertInstanceOf(Player::class, $action->player());
    }

    /** @test */
    public function can_get_chips_from_action()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, ['chips' => Chips::fromAmount(250)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals(250, $action->chips()->amount());
    }

    /** @test */
    public function can_create_action_for_check()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CHECK);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has checked.', $action->toString());
        $this->assertEquals('xLink has checked.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_call()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::CALL, ['chips' => Chips::fromAmount(250)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has called 250.', $action->toString());
        $this->assertEquals('xLink has called 250.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_raise()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::RAISE, ['chips' => Chips::fromAmount(999)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has raised 999.', $action->toString());
        $this->assertEquals('xLink has raised 999.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_fold()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::FOLD);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has folded.', $action->toString());
        $this->assertEquals('xLink has folded.', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_allin()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::ALLIN, ['chips' => Chips::fromAmount(2453)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has pushed ALL IN (2453).', $action->toString());
        $this->assertEquals('xLink has pushed ALL IN (2453).', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_sb()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::SMALL_BLIND, ['chips' => Chips::fromAmount(25)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has posted Small Blind (25).', $action->toString());
        $this->assertEquals('xLink has posted Small Blind (25).', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_bb()
    {
        $client = Client::register(Uuid::uuid4(), 'xLink');
        $player = Player::fromClient($client);

        $action = new Action($player, Action::BIG_BLIND, ['chips' => Chips::fromAmount(50)]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('xLink has posted Big Blind (50).', $action->toString());
        $this->assertEquals('xLink has posted Big Blind (50).', $action->__toString());
    }

    /** @test */
    public function can_create_action_for_flop_being_dealt()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());
        $cards = CardCollection::fromString('4♥ 4♦ 5♠');

        $action = new Action($dealer, Action::DEALT_FLOP, ['communityCards' => $cards]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('Dealer has dealt the flop (4♥ 4♦ 5♠).', $action->toString());
    }

    /** @test */
    public function can_create_action_for_turn_being_dealt()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());
        $cards = CardCollection::fromString('2♦');

        $action = new Action($dealer, Action::DEALT_TURN, ['communityCards' => $cards]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('Dealer has dealt the turn (2♦).', $action->toString());
    }

    /** @test */
    public function can_create_action_for_river_being_dealt()
    {
        $dealer = Dealer::startWork(new Deck(), new SevenCard());
        $cards = CardCollection::fromString('6♥');

        $action = new Action($dealer, Action::DEALT_RIVER, ['communityCards' => $cards]);
        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals('Dealer has dealt the river (6♥).', $action->toString());
    }
}
