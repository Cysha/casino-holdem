<?php

namespace xLink\Tests\Exceptions;

use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Uuid;
use xLink\Poker\Client;
use xLink\Poker\Exceptions\GameException;
use xLink\Poker\Game\CashGame;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Game;
use xLink\Poker\Game\Player;

class GameExceptionTest extends PHPUnit_Framework_TestCase
{
    public function test_the_invalid_id_has_a_default_message()
    {
        $expectedException = new GameException('ID passed to the Game must be a valid UUID');
        $this->assertEquals($expectedException, GameException::invalidId());
    }

    public function test_the_unexpected_suit_can_accept_custom_messages()
    {
        $expectedException = new GameException('custom message');
        $this->assertEquals($expectedException, GameException::invalidId('custom message'));
    }

    public function test_the_insufficient_funds_has_a_default_message()
    {
        $uuid = Uuid::uuid4();
        $gameName = 'game name';
        $game = CashGame::setUp($uuid, $gameName, Chips::fromAmount(100));
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(0)));

        $expectedException = new GameException(sprintf(
            '%s doesnt have sufficient funds to register for game: "%s"',
            $player->name(),
            $gameName
        ));

        $this->assertEquals($expectedException, GameException::insufficientFunds($player, $game));
    }

    public function test_the_insufficient_funds_can_accept_custom_messages()
    {
        $uuid = Uuid::uuid4();
        $gameName = 'game name';
        $game = CashGame::setUp($uuid, $gameName, Chips::fromAmount(100));
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(0)));
        $expectedMessage = 'xLink cor play "game name", the silly tart ran out of moniez';

        $expectedException = new GameException(sprintf(
            '%s cor play "%s", the silly tart ran out of moniez',
            $player->name(),
            $gameName
        ));

        $this->assertEquals($expectedException, GameException::insufficientFunds($player, $game, $expectedMessage));
    }
}
