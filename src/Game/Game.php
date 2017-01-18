<?php

namespace xLink\Poker\Game;

use Ramsey\Uuid\UuidInterface;
use xLink\Poker\Client;

interface Game
{
    /**
     * @return UuidInterface
     */
    public function id(): UuidInterface;

    public function name(): string;

    public function players(): PlayerCollection;

    public function registerPlayer(Client $client, Chips $buyInAmount = null);

    public function __toString(): string;
}
